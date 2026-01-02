{{-- Honeypot fields to detect bots --}}
<div style="position: absolute; left: -9999px; top: -9999px; opacity: 0; height: 0; width: 0; overflow: hidden;" aria-hidden="true" tabindex="-1">
    {{-- Honeypot text fields --}}
    <input type="text" name="website" value="" autocomplete="off" tabindex="-1">
    <input type="text" name="url" value="" autocomplete="off" tabindex="-1">
    <input type="text" name="phone_number" value="" autocomplete="off" tabindex="-1">
    <input type="text" name="hp_field" value="" autocomplete="off" tabindex="-1">

    {{-- Timestamp for timing check --}}
    <input type="hidden" name="_hp_timestamp" value="{{ encrypt(time()) }}">
</div>
