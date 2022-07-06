<div class="language-switcher flex justify-center pt-8 sm:justify-start sm:pt-0">
	@foreach($available_locales as $locale_name => $available_locale)
	@if($available_locale === $current_locale)
	<span class="active">{{ $locale_name }}</span>
	@else
	<a class="underline" href="/admin/language/{{ $available_locale }}">
		<span>{{ $locale_name }}</span>
	</a>
	@endif
	@endforeach
</div>