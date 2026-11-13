// Turn the static export into a clickable demo store.
// Usage: node postprocess.js <siteDir> <prefix>
const fs = require('fs');
const path = require('path');
const [DIR, PREFIX] = process.argv.slice(2);

function walk(d) {
	return fs.readdirSync(d, { withFileTypes: true }).flatMap((e) => {
		const p = path.join(d, e.name);
		if (e.isDirectory()) return e.name === '.git' ? [] : walk(p);
		return e.name.endsWith('.html') ? [p] : [];
	});
}

const TAG = `<script src="${PREFIX}/ayat-demo.js?v=11" defer></script>`;

function clean(html) {
	// Remove the earlier "open full demo" toast block.
	const i = html.indexOf('<div id="ayat-demo-toast"');
	if (i !== -1) {
		const j = html.indexOf('</script>', i);
		html = html.slice(0, i) + html.slice(j + '</script>'.length);
	}
	html = html.replace(/<script src="[^"]*ayat-demo\.js[^"]*" defer><\/script>/g, '');
	// The empty-contact admin hint is only meant for site admins (theme now hides it from visitors).
	html = html.replace(/<li>Add contact details in Appearance[^<]*<\/li>/g, '');
	// Bag icon goes to the demo bag page.
	html = html.replace(/(<a href=")[^"]*\/cart\/(" class="header-icon header-bag")/g, `$1${PREFIX}/bag/$2`);
	// Any other cart link (footer "Shopping bag") also goes to the demo bag.
	html = html.replace(/href="[^"]*\/cart\/"/g, `href="${PREFIX}/bag/"`);
	return html.replace('</body>', TAG + '\n</body>');
}

for (const f of walk(DIR)) {
	if (f.endsWith('404.html')) continue;
	fs.writeFileSync(f, clean(fs.readFileSync(f, 'utf8')));
}

// New pages built from the Collections page shell.
const shell = fs.readFileSync(path.join(DIR, 'collections', 'index.html'), 'utf8');
const views = { bag: 'Your Bag', checkout: 'Checkout', order: 'Order received', search: 'Search', 'track-order': 'Track Order' };
for (const [slug, title] of Object.entries(views)) {
	let html = shell.replace(/<main[\s\S]*<\/main>/, `<main id="main" class="site-main page-main"><div class="container"><div id="ayat-app" data-view="${slug}"></div></div></main>`);
	html = html.replace(/<title>[^<]*<\/title>/, `<title>${title} – Ayat Collections</title>`);
	html = html.replace(/<link rel="canonical"[^>]*>/, '');
	fs.mkdirSync(path.join(DIR, slug), { recursive: true });
	fs.writeFileSync(path.join(DIR, slug, 'index.html'), html);
}

// Static stand-in for My Account (the live WordPress store has real accounts).
{
	let html = shell.replace(/<main[\s\S]*<\/main>/, `<main id="main" class="site-main page-main"><div class="container" style="padding:56px 0;max-width:640px"><h1 class="page-title">My Account</h1><p class="demo-note" style="background:#f7efe2;border-left:3px solid #c79244;padding:12px 16px;color:#111">Demo store: customer accounts and order history work on the live WordPress store. In this demo you can shop as a guest and check out with Cash on Delivery.</p><p><a class="btn-ink" href="${PREFIX}/shop/">Continue shopping</a></p></div></main>`);
	html = html.replace(/<title>[^<]*<\/title>/, '<title>My Account – Ayat Collections</title>').replace(/<link rel="canonical"[^>]*>/, '');
	fs.mkdirSync(path.join(DIR, 'my-account'), { recursive: true });
	fs.writeFileSync(path.join(DIR, 'my-account', 'index.html'), html);
}

fs.copyFileSync(path.join(__dirname, 'ayat-demo.js'), path.join(DIR, 'ayat-demo.js'));
console.log('done');
