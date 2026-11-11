# Ayat Collections theme — quick setup

## 1. Install (5 minutes)
1. **Plugins → Add New**: install and activate **WooCommerce**.
2. **Appearance → Themes → Add New → Upload Theme**: choose `Ayatcollections-WordPress.zip` → **Install Now → Activate**.
3. **Appearance → Ayatcollections setup → Run setup**. Keep all boxes ticked. It:
   - creates the 4 collections (Earrings, Necklaces, Bracelets, Rings) and 8 sample products with **PKR** prices, SKUs and stock,
   - creates the **Collections** and **New Arrivals** pages and the menu (Shop, New Arrivals, Collections, About Us, Contact),
   - sets currency to PKR, turns on guest checkout and **Cash on Delivery**, and uses clean permalinks,
   - ticks "Discourage search engines" for staging.
   The page reloads by itself once per product. Wait for "All sample products imported".

Pages you get: **Home**, **Shop**, **Collections**, a page for each collection, **Product** pages, **Cart**, **Checkout**, **My Account**.

## 2. Products (all editable in WooCommerce)
- **Products → All Products**: edit name, price (PKR), sale price, SKU, stock, description and photos.
- **Products → Add New**: add your own. Tick a category so it appears in that collection.
- Variable products (bangle size, ring size): **Product data → Variable product → Attributes / Variations**.
- A new top-level category with a published product appears automatically on the homepage and the Collections page.

## 3. Demo images — replace before launch
All photos in the theme and sample products are **DEMO IMAGES** from Unsplash (Unsplash License: free for commercial use). You can find them easily:
- In **Media → Library**, they are titled **"DEMO IMAGE – replace with your own photo"**.
- In the theme folder, the files are named `assets/images/demo/demo-*.webp`, and the photographer credits are in `assets/images/demo/credits.json`.

To replace them:
- **Product photos:** open the product and change the **Product image**.
- **Collection photos:** go to **Products → Categories** and change the **Thumbnail**.
- **Homepage photos and text:** go to **Appearance → Customize → Homepage**. There are 13 sections; upload a photo to replace each demo one, and edit headings, text, buttons and links.

## 4. Logo, contact details and social links
- **Appearance → Customize → Site Identity**: the Royal A gold logo is built in. Upload a different logo here if needed, and set the **Site Icon** (browser tab icon).
- **Appearance → Customize → Store details**: WhatsApp/phone, email, address, Instagram, Facebook, TikTok, YouTube and the top announcement bar.
- The homepage **Contact** form emails the address in Store details, or the admin email if that's empty.

## 5. WooCommerce settings
- **WooCommerce → Settings → Shipping**: add a "Pakistan" zone with your delivery rate (for example a Rs 250 flat rate, or free above Rs 5,000).
- **WooCommerce → Settings → Payments**: Cash on Delivery is on. Add other gateways if you need them.
- **Accounts & Privacy**: guest checkout is on.

## 6. Before going live
1. Replace all demo images (section 3).
2. Enter your real contact details and social links.
3. Place one test order.
4. **Settings → Reading** → untick **"Discourage search engines"**.

Credits: the layout follows the section order of the Weblium "Jewelry" template; the design, code and text are original. Demo photos: Unsplash (see `credits.json`).
