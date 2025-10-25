<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $nif
 * @property string $email
 * @property string|null $phone_number
 * @property string|null $address
 * @property string $timezone
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Employee_Categories> $employeeCategories
 * @property-read int|null $employee_categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Employee> $employees
 * @property-read int|null $employees_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Punches> $punches
 * @property-read int|null $punches_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Companies newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Companies newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Companies query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Companies whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Companies whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Companies whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Companies whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Companies whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Companies whereNif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Companies wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Companies whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Companies whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCompanies {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $address
 * @property string|null $hire_date
 * @property string $email
 * @property string|null $phone_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $company_id
 * @property string|null $employee_category_id
 * @property string|null $image
 * @property string|null $position
 * @property string|null $department
 * @property string|null $salary
 * @property string $role
 * @property string|null $user_id
 * @property string $status
 * @property-read \App\Models\Companies|null $company
 * @property-read \App\Models\Employee_Categories|null $employeeCategory
 * @property-read mixed $profile_image
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Punches> $punches
 * @property-read int|null $punches_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereDepartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereEmployeeCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereHireDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereSalary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEmployee {}
}

namespace App\Models{
/**
 * @property-read \App\Models\Companies|null $company
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Employee> $employees
 * @property-read int|null $employees_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee_Categories newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee_Categories newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee_Categories query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEmployee_Categories {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $type
 * @property string $punch_time
 * @property bool $auto_closed
 * @property bool $extra_time
 * @property string|null $note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $employee_id
 * @property int $company_id
 * @property-read \App\Models\Companies $company
 * @property-read \App\Models\Employee|null $employee
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Punches newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Punches newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Punches query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Punches whereAutoClosed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Punches whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Punches whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Punches whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Punches whereExtraTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Punches whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Punches whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Punches wherePunchTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Punches whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Punches whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPunches {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $role
 * @property string|null $phone_number
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $image
 * @property-read \App\Models\Employee|null $employee
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

