#!/bin/bash

# Script to build and copy SDK to public directory for demo

echo "🔨 Building SDK..."
cd sdk/front
npm i
npm run build

echo "📦 Copying SDK to public directory..."
cd ../..
mkdir -p public/sdk
cp sdk/front/dist/index.esm.js public/sdk/paycan-sdk.js

echo "📦 Copying API-only SDK to public directory..."
cp sdk/front/dist/api.esm.js public/sdk/paycan-sdk.api.js

echo "🗜️ Creating minified versions..."
npx terser sdk/front/dist/index.esm.js --compress --mangle --output public/sdk/paycan-sdk.min.js
npx terser sdk/front/dist/api.esm.js --compress --mangle --output public/sdk/paycan-sdk.api.min.js

if [ $? -ne 0 ]; then
  echo "⚠️ Minification failed. Install terser locally: npm i -D terser"
else
  echo "✅ Minified versions created:"
  echo "  - public/sdk/paycan-sdk.min.js"
  echo "  - public/sdk/paycan-sdk.api.min.js"
fi

echo "✅ SDK is ready! Access demo at /checkout-modal-demo"
