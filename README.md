## everydns-api

I found this collection of scripts (aka everydns-api) online and decided to extend it with an _export_ script.

The library was created by Matthew Frederico (Ultrize Consulting) and is licensed under the GPL.

The export script I added will print CSV to the screen -- copy and paste it from there.

### Usage

Usage is trivial:

 * clone this repository
 * `cd everydns-api/everydns/scripts`
 * `./export.php emailaddress password`

The "email address" is the email address of your account on everydns, the password ... well, you catch my drift.

Feel free to browse the source - no credentials are stored/harvested.

### Export to bind

If you want an export to a bind zone file: pull requests welcome! ;-)

### background

When everydns.net was sold to DynDns the users were told that they could migrate to DynDns for _free_.

From what I understand there are free accounts on DynDns, so if anything that is what users should be setup
with at the bare minimum.

Of course I have no objections to pay money for a service and I also donated to EveryDns in the past. But when
DynDns decided to charge a migration fee, I've decided to take my _business_ elsewhere. I don't want to pay
five bucks which I can then maybe use towards one of their _great_ services.

Especially when I was told that this is not gonna happen since I donated to EveryDns already.

I recommend you check out [cloudflare][0], [afraid.org][1] or [zoneedit][2]. All start at free, but if you
decide to go premium, either of these services deserve your money.

[0]: http://cloudflare.com/
[1]: http://afraid.org/
[2]: http://zoneedit.com/
