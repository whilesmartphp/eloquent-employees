## Events

### EmployeeLinkedToUser

Dispatched when an employee record is linked to an authenticated user through the `link-user` API endpoint.

```php
use Whilesmart\Employees\Events\EmployeeLinkedToUser;
use Illuminate\Support\Facades\Event;

Event::listen(EmployeeLinkedToUser::class, function (EmployeeLinkedToUser $event) {
    $employee = $event->employee;
    $userId = $event->userId;

    // Add the user to the workspace
    // Send an invitation email
    // Grant default role bundles
});
```

The host application listens to this event to perform follow-up actions such as adding the user to the workspace, sending an invite, or granting a default role bundle.
