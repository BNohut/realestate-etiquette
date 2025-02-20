@php
    $src = $src ? ' src="' . $src . '"' : '';
    $alt = $alt ? ' alt="' . $alt . '"' : '';
    $id = $id ? ' id="' . $id . '"' : '';
    $title = $title ? $title : '';
    $class = $class ? ' class="' . $class . '"' : '';
    $attr = $attr ? ' ' . $attr : '';
    $width = $width ? ' width="' . $width . 'px"' : 'width="200px"';
    $height = $height ? ' height="' . $height . 'px"' : 'width="200px"';
@endphp
<label for="img">{!!$title!!}</label>
<img {!! $src !!} {!! $alt !!} {!! $title !!} {!! $id !!}
    {!! $class !!} {!! $attr !!} {!!$width!!} {!!$height!!}>

<style>
    img {
        border-radius: 1rem;
        margin-top: 10px;
        margin-bottom: 20px;
    }
    label {
        font-weight: 500;
    }
</style>
