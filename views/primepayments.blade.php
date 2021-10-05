<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<form method="POST" action="{{ $url }}" id="primePaymentsPost">
    <input type="hidden" name="action" value="{{ $action }}">
    <input type="hidden" name="project" value="{{ $project }}">
    <input type="hidden" name="sum" value="{{ $sum }}">
    <input type="hidden" name="currency" value="{{ $currency }}">
    <input type="hidden" name="innerID" value="{{ $innerID }}">
    <input type="hidden" name="email" value="{{ $email }}">
    <input type="hidden" name="sign" value="{{ $sign }}">
    <input type="hidden" name="comment" value="Пополнение баланса {{ $comment }}">
</form>
<script type="text/javascript">
    $('#primePaymentsPost').submit();
</script>
</html>
