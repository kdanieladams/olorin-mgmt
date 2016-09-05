# MGMT Administration System
## Generic admin solution for any Laravel project.

*MGMT* is a general purpose solution for any Laravel project to easily have a fully customizable administration interface within a few minutes of simple setup.  As you use this system, please bear in mind that it is still in early development.  You are likely to come across a few bugs here and there.  To make *MGMT* available to your application, there are a couple of steps to get it installed.

### Installation

1. We start by editing your `composer.json` file.  Include the privately managed VCS server in your list of repositories:

    ```javascript
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com/kdanieladams/olorin-mgmt"
        }
    ],
    ```

2. Next, require the package in your array of required packages:
    
    ```javascript
    "require": {
        "olorin/mgmt": "master"
    },
    ```
    
3. Now perform a composer update by running `$> composer update` from your project's route directory.  Composer will look first in *Packagist*, then when it doesn't find *MGMT* there, it will try GitHub.  Then, Composer will download the package into the appropriate vendor folder.

4. After the package has been downloaded to your `vendor/` folder, you'll have to [register](https://laravel.com/docs/master/providers#registering-providers) the `MgmtServiceProvider` within your application .  Open your `config/app.php` file, and add the following to the bottom of the *providers* array:

    ```php
    Olorin\Mgmt\MgmtServiceProvider::class,
    ```

5. Finally, the last thing to do is to [define error handler logic](https://laravel.com/docs/master/errors#render-method) for `MgmtException`s.  Open your `app\Exceptions\Handler.php` file, and add the following to the `render()` method before it's final return statement:
 
     ```php
     if($e instanceof \Olorin\Mgmt\MgmtException) {
         return $e->render();
     }
             
     return parent::render($request, $e);
     ```


### Initialization
1. The service provider [publishes](https://laravel.com/docs/5.3/packages#publishing-file-groups) MGMT's assets into appropriate directories within your application.  You'll need to run `php artisan vendor:publish` from your project's root directory to get the files to copy over.  
2. After that, you should run `php artisan migrate && php artisan db:seed` to create the necessary tables and fill them with some essential initial data. 

If you'd like to change MGMT's look & feel, take a gander at your `resources/views/vendor` folder for unhindered access to MGMT's entire view library.  From within, you'll be able to customize anything MGMT render's to the screen, from the broad layout all the way down to each individual form field.

### Implementation
After installing, you have to implement *MGMT* into your application.  This is easily accomplished by making your models inherit from the MgmtModel class instead of Illuminate's default Model class - `Illuminate\Database\Eloquent\Model`.

```php
use Olorin\Mgmt\MgmtModel;

class MyModel extends MgmtModel
{
```

In the case of a `User` in Laravel v5.2+, there is a new model which is inherited from - `Illuminate\Foundation\Auth\User`.  To facilitate consistency with the framework, `Olorin\Mgmt` exposes a `MgmtUserModel` class to be used for User models instead:
```php
use Olorin\Mgmt\MgmtUserModel;

class MyUser extends MgmtUserModel
{
```

### Customizing Models
After inheriting from one of the `MgmtModel` classes, you are free to customize the way each model's fields are defined.  *MGMT* works out of the box - all that is necessary for implementation is to inherit from the appropriate class.  However, due to the fluid nature of development in Laravel, *MGMT's* default implementations are fairly bare.  This is why it's important to get a handle on how every field can be customized to suit your individual application's needs.

Customization of how your model interacts with *MGMT* hinges mainly on the definition of a `getMgmtFieldsAttribute()` method:
```php
/**
 * Define some properties for displaying this model's fields
 * in the MGMT editor.
 *
 * @return array
 */
public function getMgmtFieldsAttribute()
{
    // run the base-model's method first to populate the $mgmt_fields array
    parent::getMgmtFieldsAttribute();
    
    // customize mgmt_fields here ...
    
    return $this->mgmt_fields;
}
```
This is, of course, the standard format for an *Eloquent* attribute accessor.  The importance of this is, the `mgmt_fields` attribute is what *MGMT* uses to understand how to display your model.  It should return an array of `MgmtField` objects, each defining a set of properties to represent one of your model's fields.  These properties tell *MGMT* how to determine things like administration form display for creating or editing an instance of your model, as well as validation and input translation rules.

#### Examples
Below are some examples of how a `MgmtModel` can be implemented and customized:

**User Model with No Relationships**
```php
/**
 * Define some properties for displaying this model's fields
 * in the MGMT editor.
 *
 * @return array
 */
public function getMgmtFieldsAttribute()
{
    // run the base-model's method first to populate the $mgmt_fields array
    parent::getMgmtFieldsAttribute();
    
    // we only need to modify if the model is fresh
    if($this->isFresh){
        // set fields by $field->name which will appear in MGMT's list view
        $this->setListFields("name", "email");
        
        // perform manual field property customizations
        $this->mgmt_fields["email"]->type = "email";
    }

    return $this->mgmt_fields;
}
```
**Blog Article with Complex Relationships**
```php
/**
 * Array of relationship class definitions for Mgmt.
 *
 * @var array
 */
protected $mgmt_relations = [
    'user' => ['belongsTo', 'App\User'],
    'type' => ['belongsTo', 'App\ArticleType'],
    'categories' => ['belongsToMany', 'App\ArticleCategory']
];
    
/**
 * Define some properties for displaying this model's fields
 * in the MGMT editor.
 *
 * @return array
 */
public function getMgmtFieldsAttribute()
{
    // run the base-model's method first to populate the $mgmt_fields array
    parent::getMgmtFieldsAttribute();

    // we only need to modify if the model is fresh
    if($this->isFresh){
        // set fields by $field->name which will appear in MGMT's list view
        $this->setListFields('title', 'intro', 'published_at');
        
        // set custom labels on particular fields
        $this->setFieldLabels([
            'published_at' => 'Published On',
            'user' => 'Author',
            'type' => 'Article Type',
            'categories' => 'Article Categories'
        ]);

        // perform manual field property customizations
        $this->mgmt_fields['user']->editable = false;
        $this->mgmt_fields['user']->sidebar = false;
        $this->mgmt_fields['body']->type = 'textarea-html';
        $this->mgmt_fields['published_at']->sidebar = true;
        $this->mgmt_fields['published_at']->view_options = ['date_format' => 'n/j/Y h:i a'];
        $this->mgmt_fields['categories']->view_options = ['checkboxes' => true];

        // sort the fields according to a particular order
        $sortedNames = [
            "title",
            "intro",
            "user",
            "body",
            "published_at",
            "type",
            "categories"
        ];
        usort($this->mgmt_fields, function($a, $b) use($sortedNames){
            $aInt = array_search($a->name, $sortedNames) + 1;
            $bInt = array_search($b->name, $sortedNames) + 1;

            if($aInt == $bInt){
                return 0;
            }

            return ($aInt > $bInt ? 1 : -1);
        });
    }


    return $this->mgmt_fields;
}
```

### Data Inference
*MGMT* figures out most of what it needs to know by polling your application's database for meta-information on the table assigned to the model.  The exact SQL necessary to achieve this depends heavily on what type of RDBMS your application is built on.

<span style="color:red;font-weight:bold;">Currently, the system is *only* compatible with MySQL/MariaDB databases.</span>
 
Inferring information about related-models is a bit more difficult than just reading what columns exist on the model's table.  For any `MgmtModel` to work properly, it is required that a property exists which explicitly defines relationships in order for *MGMT* to properly resolve them.  Don't worry, it's a cinche to implement:
```php
/**
 * Array of relationship class definitions for Mgmt.
 *
 * @var array
 */
protected $mgmt_relations = [
    'property_name' => ['belongsTo', 'Global\Class\Reference'],
    'other_property_name' => ['belongsToMany', 'Global\Class\OtherReference'],
    ...
];
```

### Using the Included Permission System
Using the included permissions is a cinch, and saves you from the tedious task of defining one yourself.  It's not flashy or feature-laden, but it works very well indeed.  

It simply consists of a `Permission` model and a `Role` model.  A role can have many permissions, and a permission has many roles. If you're using the `MgmtUserModel` for your users, then your user can have many roles as well.  This allows you to create a complex hierarchy of permission-levels for your application, where you can control minute functionality based on a permission, then grant that permission to a role, allowing anyone with that role to use/see it.  

1. The first step to using the included permission system is to have your `User` model inherit from the included `MgmtUserModel` in the MGMT namespace.

    ```php
    use Olorin\Mgmt\MgmtUserModel;
    
    class User extends MgmtUserModel
    {
        ...
    }
    ```
    
2. *(Optional)* You can define the GateContract for MGMT's Role/Permission system if you'd like to use the `$user->can()` function throughout your application to look up MGMT Permissions. This isn't strictly necessary, because the `MgmtUserModel` exposes a `hasPermission()` method to accomplish the same task without messing with your service providers.<br><br>If you prefer the `GateContract` method and fuller integration with your application, open your AuthServiceProvider (located by default at `app/Providers/AuthServiceProvider.php`) and add the following snippet to your `boot()` function: 

    ```php
    use Olorin\Auth\Permission;
    
    class AuthServiceProvider extends ServiceProvider
    {
        ...
        
        public function boot() 
        {
            $this->registerPolicies($gate);
            
            $permissions = Permission::all();
            
            foreach($permissions as $permission) {
                $gate->define($permission->name, function($user) use ($permission){
                    return $user->hasPermission($permission);
                });
            }
        }
    }
    ```
    
3. Now you're ready to get on using the `Olorin\Auth\Permission` model to manage granular access to your applications features.  In your controller, just check the authorized user's permissions before allowing the `ReallyCoolAdminFeature` you're trying to protect:

    ```php
    public function ReallyCoolAdminFeature()
    {
        $user = auth()->user();
        
        if($user->can('view_mgmt')) {
            // Do your really cool feature code here...
            
            return view('admin.really-cool-feature');
        }
        
        return route('403');
    }
    ```
    
4. *MGMT* only comes with one built-in permission: 'view_mgmt'.  It's meant to separate application administrators from the public and protect the *MGMT* system from being tampered with.  If you want to add more permissions, the best way would be to use the `artisan` tinker utility to create the model then save it to the database.  You can insert permissions manually, but you'll also have to define the relationship to a role, and associate the role with users.  `artisan`'s tinker utility and Eloquent work together to make this process easy, so that's what we'll use here.<br><br>Open up a console window in your project root, and start the `artisan` tinker utility:
 
     ```console
     /var/www/project-root $> php artisan tinker
     Tinker - Laravel v5.2.x - by That Dude Who Wrote Tinker
     >> $permission = new Olorin\Auth\Permission()
     ...
     >> $permission->name = "my_perm"
     my_perm
     >> $permission->save()
     Yay!  You saved a permission
     $role = Olorin\Auth\Role::find(my_role_id)
     ...
     $role->grantPermission($permission)
     ...
     $user = App\User::find(my_user_id)
     ...
     $user->assignRole($role)
     ...
     Yay!  You gave someone a role!
     ```
 