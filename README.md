# MGMT Administration System
## Generic admin solution for any Laravel project.

**<span style="color:red;">This repository is hosted on a private VCS server, and is not intended for public distribution</span>**

*MGMT* is a general purpose solution for any Laravel project to easily have a fully customizable administration interface within a few minutes of simple setup.  As you use this system, please bear in mind that it is still in early development.  You are likely to come across a few bugs here and there.

### Installation
To make *MGMT* available to your application, there are a couple of steps to get it installed.
1. We start by editing your `composer.json` file.  Include the privately managed VCS server in your list of repositories:
```
"repositories": [
    {
        "type": "vcs",
        "url": "git@52.5.160.236:dan/mgmt.git"
    }
],
```
2. Next, require the package in your array of required packages:
```
"require": {
    "dan/mgmt": "master"
},
```
3. Now perform a composer update by running `$> composer update` from your project's route directory.  Composer will look first in *Packagist*, then when it doesn't find *MGMT* there, it will try the private VCS server.  Then, Composer will download the package into the appropriate vendor folder.

4. After the package has been downloaded to your `vendor/` folder, you'll have to [register](https://laravel.com/docs/master/providers#registering-providers) the `MgmtServiceProvider` within your application .  Open your `config/app.php` file, and add the following to the bottom of the *providers* array:
```
Olorin\Mgmt\MgmtServiceProvider::class,
```

5. Finally, the last thing to do is to [define error handler logic](https://laravel.com/docs/master/errors#render-method) for `MgmtException`s.  Open your `app\Exceptions\Handler.php` file, and add the following to the `render()` method before it's final return statement:
 ```
 if($e instanceof \Olorin\Mgmt\MgmtException) {
     return $e->render();
 }
         
 return parent::render($request, $e);
 ```

### Implementation
After installing, you have to implement *MGMT* into your application.  This is easily accomplished by making your models inherit from the MgmtModel class instead of Illuminate's default Model class - `Illuminate\Database\Eloquent\Model`.
```
use Olorin\Mgmt\MgmtModel;

class MyModel extends MgmtModel
{
```

In the case of a `User` in Laravel v5.2+, there is a new model which is inherited from - `Illuminate\Foundation\Auth\User`.  To facilitate consistency with the framework, `Olorin\Mgmt` exposes a `MgmtUserModel` class to be used instead:
```
use Olorin\Mgmt\MgmtUserModel;

class MyUser extends MgmtUserModel
{
```

### Customization
After inheriting from one of the `MgmtModel` classes, you are free to customize the way each model's fields are defined.  *MGMT* works out of the box - all that is necessary for implementation is to inherit from the appropriate class.  However, due to the fluid nature of development in Laravel, *MGMT's* default implementations are fairly bare.  This is why it's important to get a handle on how every field can be customized to suit your individual application's needs.

Customization of how your model interacts with *MGMT* hinges mainly on the definition of a `getMgmtFieldsAttribute()` method:
```
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
This is, of course, the standard format for an *Eloquent* attribute accessor.  The importance of this is, the `mgmt_fields` attribute is what *MGMT* uses to understand how to display your model.  It should return an array of `MgmtField` objects, each defining a set of properties to represent one of your model's field.  These properties tell *MGMT* how to determine things like administration form display for creating or editing an instance of your model, as well as validation and input translation rules.

#### Data Inference
*MGMT* figures out most of what it needs to know by polling your application's database for meta-information on the table assigned to the model.  The exact SQL necessary to achieve this depends heavily on what type of RDBMS your application is built on.

<span style="color:red;font-weight:bold;">Currently, the system is *only* compatible with MySQL databases.</span>
 
Inferring information about related-models is a bit more difficult than just reading what columns exist on the model's table.  *MGMT* requires a property explicitly defining relationships in order to properly resolve them.  Don't worry, it's a cinche to implement:
```
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
  
#### Examples
Below are some examples of how a `MgmtModel` can be customized:

**User Model with No Relationships**
```PHP
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
```PHP
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
