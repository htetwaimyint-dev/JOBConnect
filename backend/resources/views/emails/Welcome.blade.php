<x-mail::message>
# Welcome, {{ $userName }}

Your account is ready. You can now search jobs, submit applications, and track interview updates in one place.

<x-mail::button :url="$loginUrl">
Go to Dashboard
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>