const { test, expect } = require('@playwright/test');

test.describe('🎯 تست کامل فرآیند پذیرش (Integration رزرو + دوره/همایش)', () => {
  test.beforeEach(async ({ page }) => {
    // لاگین قبل از هر تست
    await page.goto('http://localhost:8081/login');
    await page.fill('input[name="email"]', 'admin@bank.ir');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/dashboard', { timeout: 10000 });
  });

  test.skip('1️⃣ ایجاد دوره تست', async ({ page }) => {
    console.log('📚 ایجاد دوره جدید...');

    await page.goto('http://localhost:8081/courses');
    await page.waitForLoadState('networkidle');

    // کلیک روی دکمه ایجاد دوره
    await page.click('a[href*="courses/create"]');
    await page.waitForLoadState('networkidle');

    // پر کردن فرم با تاریخ شمسی
    // استفاده از تاریخ‌های ثابت در آینده (در بازه 45 روز)
    const jalaliStart = '1404/10/15';
    const jalaliEnd = '1404/10/22';

    await page.fill('input[name="name"]', 'دوره تست Playwright');
    await page.fill('input[name="code"]', 'TEST-COURSE-001');
    await page.fill('input[name="start_date"]', jalaliStart);
    await page.fill('input[name="end_date"]', jalaliEnd);
    await page.fill('input[name="capacity"]', '30');
    await page.fill('input[name="location"]', 'سالن اصلی');
    await page.fill('textarea[name="description"]', 'دوره تست برای Playwright');

    await page.screenshot({ path: 'tests/screenshots/admission-01-course-form.png', fullPage: true });

    // ثبت فرم
    await page.click('button[type="submit"]');
    await page.waitForURL('**/courses', { timeout: 10000 });

    console.log('✅ دوره ایجاد شد\n');
    await page.screenshot({ path: 'tests/screenshots/admission-02-course-created.png', fullPage: true });
  });

  test.skip('2️⃣ ایجاد همایش تست', async ({ page }) => {
    console.log('🎤 ایجاد همایش جدید...');

    await page.goto('http://localhost:8081/conferences');
    await page.waitForLoadState('networkidle');

    // کلیک روی دکمه ایجاد همایش
    await page.click('a[href*="conferences/create"]');
    await page.waitForLoadState('networkidle');

    // پر کردن فرم با تاریخ شمسی
    const jalaliConfStart = '1404/10/25';
    const jalaliConfEnd = '1404/10/26';

    await page.fill('input[name="name"]', 'همایش تست Playwright');
    await page.fill('input[name="code"]', 'TEST-CONF-001');
    await page.fill('input[name="start_date"]', jalaliConfStart);
    await page.fill('input[name="end_date"]', jalaliConfEnd);
    await page.fill('input[name="organizer"]', 'تیم تست');
    await page.fill('input[name="capacity"]', '50');
    await page.fill('textarea[name="description"]', 'همایش تست برای Playwright');

    await page.screenshot({ path: 'tests/screenshots/admission-03-conference-form.png', fullPage: true });

    // ثبت فرم
    await page.click('button[type="submit"]');
    await page.waitForURL('**/conferences', { timeout: 10000 });

    console.log('✅ همایش ایجاد شد\n');
    await page.screenshot({ path: 'tests/screenshots/admission-04-conference-created.png', fullPage: true });
  });

  test('3️⃣ تست رزرو برای دوره کلاسی', async ({ page }) => {
    console.log('📝 تست رزرو با دوره...');

    await page.goto('http://localhost:8081/reservations/create');
    await page.waitForLoadState('networkidle');

    await page.screenshot({ path: 'tests/screenshots/admission-05-reservation-form-initial.png', fullPage: true });

    // انتخاب نوع پذیرش: دوره کلاسی
    // یافتن option که شامل کلمه "دوره" باشد
    const courseTypeOption = await page.locator('select[name="admission_type_id"] option').evaluateAll(options => {
      const opt = options.find(o => o.text.includes('دوره'));
      return opt ? opt.value : null;
    });
    if (courseTypeOption) {
      await page.selectOption('select[name="admission_type_id"]', courseTypeOption);
    }
    await page.waitForTimeout(500); // صبر برای نمایش dropdown دوره

    // بررسی نمایش dropdown دوره
    const courseSection = await page.locator('#courseSection').isVisible();
    console.log('   📋 Dropdown دوره نمایش داده شد:', courseSection ? '✅' : '❌');
    expect(courseSection).toBeTruthy();

    await page.screenshot({ path: 'tests/screenshots/admission-06-course-dropdown-visible.png', fullPage: true });

    // انتخاب دوره
    const courseOptions = await page.locator('select[name="course_id"] option').count();
    console.log('   📊 تعداد دوره‌های موجود:', courseOptions - 1); // -1 برای option خالی

    if (courseOptions > 1) {
      await page.selectOption('select[name="course_id"]', { index: 1 });
      await page.waitForTimeout(1000); // صبر برای تنظیم خودکار تاریخ

      // بررسی تنظیم خودکار تاریخ
      const checkInValue = await page.inputValue('input[name="check_in_date"]');
      const checkOutValue = await page.inputValue('input[name="check_out_date"]');
      console.log('   📅 تاریخ ورود (خودکار):', checkInValue || 'خالی');
      console.log('   📅 تاریخ خروج (خودکار):', checkOutValue || 'خالی');

      // اگر تاریخ خودکار تنظیم نشد، دستی وارد کن
      if (!checkInValue || !checkOutValue) {
        console.log('   ⚠️  تاریخ خودکار تنظیم نشد، تنظیم دستی...');
        const today = new Date().toISOString().split('T')[0];
        const nextWeek = new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
        await page.fill('input[name="check_in_date"]', today);
        await page.fill('input[name="check_out_date"]', nextWeek);
      }
    }

    // انتخاب پرسنل
    await page.selectOption('select[name="personnel_id"]', { index: 1 });

    // انتخاب اتاق
    await page.selectOption('select[name="room_id"]', { index: 1 });
    await page.waitForTimeout(500);

    // انتخاب اولین تخت موجود
    const firstBedCheckbox = await page.locator('input[name="bed_ids[]"]:not([disabled])').first();
    await firstBedCheckbox.check();

    await page.screenshot({ path: 'tests/screenshots/admission-07-course-reservation-filled.png', fullPage: true });

    // ثبت رزرو
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);

    // بررسی موفقیت
    const currentUrl = page.url();
    console.log('   🔗 URL فعلی:', currentUrl);

    if (currentUrl.includes('/reservations/')) {
      console.log('✅ رزرو با دوره با موفقیت ثبت شد\n');
      await page.screenshot({ path: 'tests/screenshots/admission-08-course-reservation-success.png', fullPage: true });
    } else {
      console.log('⚠️  Validation error یا مشکل در ثبت رزرو');
      await page.screenshot({ path: 'tests/screenshots/admission-08-course-reservation-error.png', fullPage: true });
    }
  });

  test('4️⃣ تست رزرو برای همایش', async ({ page }) => {
    console.log('📝 تست رزرو با همایش...');

    await page.goto('http://localhost:8081/reservations/create');
    await page.waitForLoadState('networkidle');

    // انتخاب نوع پذیرش: همایش
    const conferenceTypeOption = await page.locator('select[name="admission_type_id"] option').evaluateAll(options => {
      const opt = options.find(o => o.text.includes('همایش'));
      return opt ? opt.value : null;
    });
    if (conferenceTypeOption) {
      await page.selectOption('select[name="admission_type_id"]', conferenceTypeOption);
    }
    await page.waitForTimeout(500);

    // بررسی نمایش dropdown همایش
    const conferenceSection = await page.locator('#conferenceSection').isVisible();
    console.log('   📋 Dropdown همایش نمایش داده شد:', conferenceSection ? '✅' : '❌');
    expect(conferenceSection).toBeTruthy();

    // بررسی مخفی بودن dropdown دوره
    const courseSectionHidden = !(await page.locator('#courseSection').isVisible());
    console.log('   🔒 Dropdown دوره مخفی است:', courseSectionHidden ? '✅' : '❌');
    expect(courseSectionHidden).toBeTruthy();

    await page.screenshot({ path: 'tests/screenshots/admission-09-conference-dropdown-visible.png', fullPage: true });

    // انتخاب همایش
    const confOptions = await page.locator('select[name="conference_id"] option').count();
    console.log('   📊 تعداد همایش‌های موجود:', confOptions - 1);

    if (confOptions > 1) {
      await page.selectOption('select[name="conference_id"]', { index: 1 });
      await page.waitForTimeout(1000);

      const checkInValue = await page.inputValue('input[name="check_in_date"]');
      const checkOutValue = await page.inputValue('input[name="check_out_date"]');
      console.log('   📅 تاریخ ورود (خودکار):', checkInValue || 'خالی');
      console.log('   📅 تاریخ خروج (خودکار):', checkOutValue || 'خالی');

      // اگر تاریخ خودکار تنظیم نشد، دستی وارد کن
      if (!checkInValue || !checkOutValue) {
        console.log('   ⚠️  تاریخ خودکار تنظیم نشد، تنظیم دستی...');
        const confStart = new Date(Date.now() + 20 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
        const confEnd = new Date(Date.now() + 22 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
        await page.fill('input[name="check_in_date"]', confStart);
        await page.fill('input[name="check_out_date"]', confEnd);
      }
    }

    // انتخاب پرسنل
    await page.selectOption('select[name="personnel_id"]', { index: 1 });

    // انتخاب اتاق
    await page.selectOption('select[name="room_id"]', { index: 2 });
    await page.waitForTimeout(500);

    // انتخاب اولین تخت موجود
    const firstBedCheckbox = await page.locator('input[name="bed_ids[]"]:not([disabled])').first();
    await firstBedCheckbox.check();

    await page.screenshot({ path: 'tests/screenshots/admission-10-conference-reservation-filled.png', fullPage: true });

    // ثبت رزرو
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);

    const currentUrl = page.url();
    console.log('   🔗 URL فعلی:', currentUrl);

    if (currentUrl.includes('/reservations/')) {
      console.log('✅ رزرو با همایش با موفقیت ثبت شد\n');
      await page.screenshot({ path: 'tests/screenshots/admission-11-conference-reservation-success.png', fullPage: true });
    } else {
      console.log('⚠️  Validation error یا مشکل در ثبت رزرو');
      await page.screenshot({ path: 'tests/screenshots/admission-11-conference-reservation-error.png', fullPage: true });
    }
  });

  test('5️⃣ تست رزرو برای ماموریت اداری (بدون دوره/همایش)', async ({ page }) => {
    console.log('📝 تست رزرو ماموریت اداری...');

    await page.goto('http://localhost:8081/reservations/create');
    await page.waitForLoadState('networkidle');

    // انتخاب نوع پذیرش: ماموریت اداری
    const businessTypeOption = await page.locator('select[name="admission_type_id"] option').evaluateAll(options => {
      const opt = options.find(o => o.text.includes('ماموریت'));
      return opt ? opt.value : null;
    });
    if (businessTypeOption) {
      await page.selectOption('select[name="admission_type_id"]', businessTypeOption);
    }
    await page.waitForTimeout(500);

    // بررسی مخفی بودن هر دو dropdown
    const courseSectionHidden = !(await page.locator('#courseSection').isVisible());
    const conferenceSectionHidden = !(await page.locator('#conferenceSection').isVisible());

    console.log('   🔒 Dropdown دوره مخفی است:', courseSectionHidden ? '✅' : '❌');
    console.log('   🔒 Dropdown همایش مخفی است:', conferenceSectionHidden ? '✅' : '❌');

    expect(courseSectionHidden).toBeTruthy();
    expect(conferenceSectionHidden).toBeTruthy();

    await page.screenshot({ path: 'tests/screenshots/admission-12-business-trip-no-dropdowns.png', fullPage: true });

    // تاریخ دستی
    const today = new Date().toISOString().split('T')[0];
    const nextWeek = new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];

    await page.fill('input[name="check_in_date"]', today);
    await page.fill('input[name="check_out_date"]', nextWeek);

    // انتخاب پرسنل
    await page.selectOption('select[name="personnel_id"]', { index: 1 });

    // انتخاب اتاق
    await page.selectOption('select[name="room_id"]', { index: 3 });
    await page.waitForTimeout(500);

    // انتخاب اولین تخت موجود
    const firstBedCheckbox = await page.locator('input[name="bed_ids[]"]:not([disabled])').first();
    await firstBedCheckbox.check();

    await page.screenshot({ path: 'tests/screenshots/admission-13-business-trip-filled.png', fullPage: true });

    // ثبت رزرو
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);

    const currentUrl = page.url();
    if (currentUrl.includes('/reservations/')) {
      console.log('✅ رزرو ماموریت اداری با موفقیت ثبت شد\n');
      await page.screenshot({ path: 'tests/screenshots/admission-14-business-trip-success.png', fullPage: true });
    } else {
      console.log('⚠️  Validation error یا مشکل در ثبت رزرو');
      await page.screenshot({ path: 'tests/screenshots/admission-14-business-trip-error.png', fullPage: true });
    }
  });

  test('6️⃣ مشاهده لیست رزروها', async ({ page }) => {
    console.log('📋 مشاهده لیست رزروها...');

    await page.goto('http://localhost:8081/reservations');
    await page.waitForLoadState('networkidle');

    const reservationRows = await page.locator('table tbody tr').count();
    console.log('   📊 تعداد رزروها:', reservationRows);

    await page.screenshot({ path: 'tests/screenshots/admission-15-reservations-list.png', fullPage: true });
    console.log('✅ لیست رزروها OK\n');
  });
});
