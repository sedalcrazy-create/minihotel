#!/bin/bash

echo "🐳 Export کردن Docker Image برای نصب آفلاین..."
echo ""

# Build image if not exists
echo "1️⃣ ساخت Docker Image..."
docker-compose build

# Get image name
IMAGE_NAME="minihotel-app"
VERSION=$(date +%Y%m%d_%H%M%S)
OUTPUT_FILE="minihotel-image-${VERSION}.tar"

echo ""
echo "2️⃣ Export کردن Image..."
docker save -o "${OUTPUT_FILE}" "${IMAGE_NAME}:latest"

if [ $? -eq 0 ]; then
    FILE_SIZE=$(du -h "${OUTPUT_FILE}" | cut -f1)
    echo ""
    echo "✅ موفق! Image ذخیره شد:"
    echo "   📦 فایل: ${OUTPUT_FILE}"
    echo "   📊 حجم: ${FILE_SIZE}"
    echo ""
    echo "📋 فایل‌های مورد نیاز برای انتقال:"
    echo "   ✓ ${OUTPUT_FILE}"
    echo "   ✓ docker-compose.yml"
    echo "   ✓ .env.example"
    echo "   ✓ database/ (پوشه)"
    echo "   ✓ scripts/import-image.bat"
    echo ""
    echo "🚀 این فایل‌ها را روی فلش کپی کنید و به سیستم آفلاین منتقل کنید."
else
    echo ""
    echo "❌ خطا در Export!"
    exit 1
fi
