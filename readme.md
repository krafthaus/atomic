### Configure components

```php
// config/atomic.php
return [
    'components' => [
        'header' => App\Components\HeaderComponent::class,
        'other_component' => App\Components\OtherComponent::class,
        'one_more_component' => App\Components\OneMoreComponent::class,
    ]
]
```

### Creating components

```php
namespace App\Components;

use KraftHaus\Atomic\Component;

class HeaderComponent extends Component
{
    protected $validate = [
        'title' => 'required|...',
    ];

    public function view()
    {
        return view('components.header');
    }
}
```

### Developing entities

```php
use KraftHaus\Atomic\Entity;
use KraftHaus\Atomic\Component;

class MyAwesomeEntity extends Entity
{
    public function indexAction(Component $component)
    {
        $component->header([
            'props' => ['title' => $this->users->first()->name],
            'children' => [
                'other_component' => [
                    'props' => ...
                    'children' => ...
                ]
            ]
        ]);

        $component->other_component([
            'children' => function ($otherComponent) {
                $otherComponent->include(__CLASS__, 'subComponentAction');
            }
        ]);
    }

    /**
     * Demonstrates seperating components by using other methods
     */
    public function subComponentAction(OtherComponent $component)
    {
        $component->oneMoreComponent(['props' => ['my-property' => 'awesome']]);
    }
}
```

### Rendering entities

```php
use KraftHaus\Atomic\Support\Facades\Atomic;

return Atomic::make(MyAwesomeEntity::class, 'indexAction')->with([
    'users' => App\User::all()
])->render(;
```