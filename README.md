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

Without this package, to simulate the eloquent events, you will end with:

```php

class User extends Authenticatable
    
    public function verify($mobile)
    {
        $userMobile = new UserMobile([
            'mobile' => $mobile, 'status' => self::STATUS_VERIFIED
        ]);

        if(app()->events->until(
            event(new MobileVerifying($userMobile)) !== false
        )){
            $userMobile = $this->mobiles()->save($userMobile);
            event(new MobileVerified($userMobile));
            return $userMobile;
        }
        
        return false;
    }
}
``` 

To simplify your `User` model, declare `action{MethodName}` method and remove all event related codes, the package will automatically fire `before{Method}` and `after{Method}` events when `$user->verify($mobile)` invoked.

```php
<?php

class User extends Authenticatable
{
    
    public function actionVerify($mobile) {
        return $userMobile = $this->mobiles()->save([
            'mobile' => $mobile, 'status' => self::STATUS_VERIFIED
        ]);
    }
}

```

## Use dispatchesEvents

As eloquent events, you can map the dispatched events using `$dispatchesEvents` proparity

```php
<?php

class User extends Authenticatable
{
    
    public function actionVerify($mobile) { }
    
    protected $dispatchesEvents = [
        'beforeVerify' => MobileVerifying::class,
        'afterVerify' => MobileVerified::class
    ];
}

```

## Use EventServiceProvider

You can map events to listener as usual in EventServiceProvider, you can use both string event name or the mapped events from the `dispatchesEvents`:

```php
<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    // ...
    
    protected $listen = [
        MobileVerifying::class => [ SomeListener::class ],
        MobileVerified::class => [ SomeListener::class ],
        
        // or
        
        'eloquent.beforeVerify: App\User' => [ SomeListener::class ],
        'eloquent.afterVerify: App\User' => [ SomeListener::class ],
    ];
    
    // ...
}

```

## Use Model Observer
As eloquent observable, you can map the observable events using `$observables` proparity

```php
<?php

class User extends Authenticatable
{
    
    public function actionVerify($mobile) { }
    
    protected $observables = [
        'beforeVerify', 'afterVerify'
    ];
}

```

and then you cn add beforeVerify and afterVerify functions in the ModelObserver class same as other eloqunt functions.

```php
<?php

namespace App\Observers;

use App\User;

class UserObserver
{
    public function created(User $user)
    {
        //
    }

    public function updated(User $user)
    {
        //
    }

    public function deleted(User $user)
    {
        //
    }

    public function restored(User $user)
    {
        //
    }

    public function forceDeleted(User $user)
    {
        //
    }

    public function beforeVerify(User $user)
    {
        dump('UserObserver::beforeVerify');
    }

    public function afterVerify(User $user)
    {
        dump('UserObserver::afterVerify');
    }

}


```
