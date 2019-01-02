# Laravel Eloquent Custom Actions.

When working with big projects, it's important to split the big task into smaller one, and in many cases it's very recommended to work with events to execute each part of the code. Take for example, we need to add user mobile in the system, but adding the user mobile has some attaches events to be accomplished before and after the user mobile added. For example,
once the user verified new mobile number, we must delete not verified users linked to this number, delete all related activation codes, link mobile with the current user, delete invalid invitation based on some criteria, and finally notify the user if he/she have new invitation based on the new number.

Like laravel scope, you can add the required event by prefixing the function with the world action, like "actionVerify"", and the package will fire the beforeVerify and afterVerify events. All you need is to listen to these events and map put your functionality in the event listener for each event.

## Installation

You can install the package via composer:

```bash
composer require digitalcloud/eloquent-custom-actions
```

## Usage Example


In the user model, we declare actionVerify function and add the logic you need, the package will fire beforeVerify and  afterVerify events automatically, so you need to map event and event listener to these events.

```php
<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    
    public function actionVerify($data = []) {
        dump('User Verify.');
    }
    
    // map the built-in events to custom events
    protected $dispatchesEvents = [
        'beforeVerify' => \App\Events\BeforeUserVerify::class,
        'afterVerify' => \App\Events\AfterUserVerify::class
    ];
}

```

In our EventServiceProvider, we need to add listener to the $listen property, to allow dispatching events to it's listener:

```php
<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    // ...
    
    protected $listen = [
        \App\Events\BeforeUserVerify::class => [\App\Listeners\BeforeUserVerify::class],
        \App\Events\AfterUserVerify::class => [\App\Listeners\AfterUserVerify::class],
    ];
    
    // ...
}

```

And now, you just need to call $user->verify() to execute the beforeVerify nad then the code, and finally the afterVerify event.

```php

<?php

$user = \App\User::find(1);
$user->verify();

```

The output is:

```
"App\Listeners\BeforeUserVerify::handle"

"User Verify."

"App\Listeners\AfterUserVerify::handle"
```
