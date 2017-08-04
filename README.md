# laravel-docs
Docs reader for laravel, currently supported extensions is: `.md`
Helpful to use it as `knowledge base` or `faq` of your project or features.


# Features

- Render `.md` docs
- Security: Configure access
- You can request/suggest more features


# How to use

- Install:

	- `git` : `git clone https://github.com/AnandPilania/laravel-docs.git`
	- `composer` : `composer install anandpilania/laravel-docs`

	- Register `Provider` to `config/app.php` :
	- `AP\Docs\ServiceProvider::class`
	
	Instead of `Facade`, I used `Contract`, so `Docs` can be directly accessible to any `class`.

	
- Publish the `config` and `resources`:

	- `php artisan vendor:publish`

	
- Configure package according to your need (`app/docs.php`):

	- `disk.root` => Root path of all documents (by default: storage/app/docs)
	
	`http` : Responsible for default routes
	- `prefix` => Route prefix (default: /docs)
	- `middleware` => By default, `web` is used as a middleware group
	
	`default' : This section responsible for rendering the default `doc`
	- `vendor` => Default: storage/app/docs/laravel
	- `version` => Default: 5.4
	- `page` => Load default (installation) file while accessing //HOSTNAME/docs/laravel/5.4. 
	- `index` => Default `index` file
	- `extension` => Currently this package supports only `.md`, so don't change it.
	
	`security` : Limit the access of `docs`
	- `enabled` => Its up to you, to enable this feature or not. `true`||`false`
	- `file` => Configure the file name of `security` according to your wish. default `security.json`
	Add this `security.file` to the `vendor` directory.
	
	`extensions` :
	- `supported` => Add extensions those are supported for render (currently `.md`), remaning extensions will excluded
	- `excluded` => Files containing these extensions will not displayed in the list
	

- Sample `security.json` (ex: /storage/docs/app/laravel/security.json):
	
	{
		"roles": ["users"],
		"permissions": ["docs.show.versions"]
	}
	
	
# NOTE: Default `route` are hard-coded to the `ServiceProvider` file of the package.