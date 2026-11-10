# Ayatcollections theme — setup guide

## 1. Install
1. Install and activate **WooCommerce** (Plugins → Add New). Skip or finish its setup wizard.
2. Go to **Appearance → Themes → Add New → Upload Theme**, choose `Ayatcollections-WordPress.zip`, then click **Install Now → Activate**.

## 2. Run the one-click setup (recommended)
Go to **Appearance → Ayatcollections setup**, leave every box ticked and click **Run setup**. It:
- imports the 8 sample products and 4 collections (Earrings, Necklaces, Bracelets, Rings),
- copies the bundled photos into the **Media Library** and assigns them as product and category images (with alt text),
- creates the **Collections** and **New Arrivals** pages and a primary menu (Shop · New Arrivals · Collections),
- sets currency to **PKR** with no decimals, turns on guest checkout, stock management and **Cash on Delivery**,
- switches permalinks to `/%postname%/`,
- ticks **Discourage search engines** (staging).

Products are imported **one per step**: the page reloads by itself 8 times, so each request stays short on shared hosting. Wait for **"All sample products imported."** If the page stops mid-way, click **Continue**.

You can run it again safely: products whose SKU already exists are skipped.

## 3. Optional: import with the WooCommerce CSV instead
Use this if you prefer the native importer (or to load products onto another site).
1. Open `sample-data/ayatcollections-products.csv` and replace `YOUR-STAGING-DOMAIN` with your site's domain (for example `staging.ayatcollections.com`). The image URLs then point to the photos inside the installed theme folder: `/wp-content/themes/ayatcollections/assets/images/products/`.
2. Go to **Products → Import**, choose the CSV, then click **Continue → Run the importer**.
3. WooCommerce downloads each image URL into the Media Library and sets it as the product photo. The site must be reachable from itself over HTTPS/HTTP. If your staging site is password-protected, use the one-click setup instead, because it copies the files directly.

The CSV has 8 products: 6 simple products and 2 variable products (bangle size 2.4/2.6/2.8, ring size 6/7/8), with 3 variations each. Each one has a SKU, PKR price and stock.

## 4. Products and photos
- **Add a product:** go to Products → Add New. Set the price in PKR, the SKU and stock (Inventory tab), and a **Product image** and **Product gallery**. Tick a category.
- **Replace a sample photo:** open the product and click the image under **Product image**. Then use **Remove**, **Set product image** and upload. Write alt text in the media panel.
- **Variants:** in Product data, choose *Variable product*, then add an attribute under Attributes (tick *Used for variations*) and create the variations.

## 5. Collections appear automatically
Create a **top-level** category (Products → Categories, Parent = None) and publish at least one product in it. It then appears on the homepage "Shop by collection" grid, on the **Collections** page, in the shop filter chips and in the footer. A category that only has draft products stays hidden. New categories are added at the end of the list. Its card uses the category's **Thumbnail**, or the newest product photo when no thumbnail is set. Drag categories on the Categories screen to reorder them. Each collection has its own page at `/product-category/<slug>/`.

## 6. Logo, slides, focal points, headlines and buttons
**Appearance → Customize**
- **Site Identity → Logo:** upload your logo (a transparent PNG or SVG converted to PNG, about 320×80).
- **Homepage slider → Slider settings:** time per slide (5000–6000 ms).
- **Homepage slider → Slide 1/2/3:**
  - *Show this slide*, *Slide photo* (wide, about 1920×820), and an optional *Mobile photo*.
  - *Image alt text*, *Small label*, *Headline*, *Supporting line*, *Button label* and *Button link*. A link can be relative, such as `/product-category/rings/` or `/new-arrivals/`, or a full URL.
  - **Focal points:** horizontal and vertical %, set separately for desktop and mobile. Move them until the jewellery stays in view in the live preview. Use 0 for left/top and 100 for right/bottom. The sample photos have the jewellery on the right, so their defaults are about 70–80% horizontal.
- **Store details:** homepage intro heading and text (leave the heading empty to hide the intro), announcement bar text, footer description, phone, email and social links.

**Menus:** Appearance → Menus (Primary and Footer locations).

## 7. Configure WooCommerce
- **Shipping:** WooCommerce → Settings → Shipping → Add zone "Pakistan" → add *Flat rate* (for example Rs 250) and/or *Free shipping* (for example orders over Rs 5,000).
- **Payments:** WooCommerce → Settings → Payments. Cash on Delivery is enabled by the setup; you can change its title or limit it to shipping methods. Add a card or wallet gateway plugin if you need one, then enable it here.
- **Guest checkout:** WooCommerce → Settings → Accounts & Privacy → "Allow customers to place orders without an account" (on after setup).
- **Inventory:** WooCommerce → Settings → Products → Inventory (low-stock alerts, hold stock).
- **Cart and Checkout pages:** both native shortcode and block versions are styled.

## 8. SEO and search indexing
- The theme uses WordPress title tags and semantic headings, and works with Yoast SEO, Rank Math, SEOPress and AIOSEO. Set meta descriptions and sitemaps in your SEO plugin.
- **Staging stays out of search results:** Settings → Reading → **"Discourage search engines from indexing this site"** is ticked by the setup. A red **"Staging: not indexed"** badge shows in the admin bar while it is on.
- **Before launch:** untick that box and save. The badge turns green ("Search: indexable"). Then submit your sitemap in Google Search Console.
- Permalinks: Settings → Permalinks → *Post name*.

## 9. Before going live (checklist)
- Place one real test order with Cash on Delivery on staging, and one with each online gateway you enable.
- Set up your shipping zones and rates, then check them at checkout.
- Replace the sample photos and text with your own, or delete the sample products (SKUs starting `AYC-`).
- Add your logo, contact details and a privacy policy page.
- Untick "Discourage search engines" (section 8).

## 10. Performance notes
- Bundled photos are WebP. WordPress creates responsive sizes for anything you upload.
- The first hero image is preloaded with `fetchpriority="high"`, while other slides and below-the-fold images load lazily.
- For production, add a page-cache plugin and serve images through your host's CDN if one is available.
