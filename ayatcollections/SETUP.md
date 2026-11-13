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

- **Jewellery-box hero:** Appearance → Customize → Homepage → 1. Hero → "Jewellery inside the box". A transparent PNG/WebP of one piece looks best. The box opens and closes on a 6-second loop with a Pause button; visitors with reduced motion see the open box straight away.
- **Shop the Look:** same panel, section 2: photo, heading, text and the product slugs to link (comma separated).

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

## 7. Customer reviews (Instagram, Facebook, TikTok, WhatsApp, Google)
- **Add by hand:** WP Admin → **Customer Reviews → Add customer review**. Type the customer's name as the title and their words in the box, then pick the stars, where it was posted, the product (optional) and the link to the original post. A screenshot can go in Featured image. Reviews show on the homepage ("What Our Customers Say") and on the product's page.
- **Auto-sync:** install a reviews plugin that connects to your accounts (for example a Google / Facebook reviews widget plugin), copy its shortcode and paste it in **Appearance → Customize → Homepage → Reviews widget shortcode**. It appears under your reviews. Instagram comments, TikTok comments and WhatsApp chats cannot be pulled in automatically by the theme; add the best ones by hand.
- **Before launch:** delete the reviews marked "sample" in Customer Reviews. Only publish reviews real customers actually gave.
- Customers can also leave star reviews on each product (WooCommerce → Settings → Products → "Enable product reviews").

## 8. Know the moment an order comes in
Go to **WooCommerce → Order alerts**:
1. **Email:** type the email(s) that should get a "New order" email for every order. If emails don't arrive, install the free **WP Mail SMTP** plugin and connect your Gmail.
2. **WhatsApp alert to your phone:** save +34 644 66 32 62 (CallMeBot) in your contacts and send it the message "I allow callmebot to send me messages". Paste the API key it replies with, enter your WhatsApp number, tick **On** and press **Save and send test WhatsApp**. Every new order then arrives on WhatsApp with the customer name, phone, city, items and total.
3. **"Confirm order on WhatsApp" button:** shown to the customer after ordering; it opens WhatsApp with their order details ready to send to your store number.
4. **Phone app:** install the free **WooCommerce** app (Android / iPhone) and log in with your website for a sound + notification on every order.
All orders are also listed in **WooCommerce → Orders** (the menu shows how many new orders are waiting).
