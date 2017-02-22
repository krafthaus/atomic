@foreach ($component->children as $child)
    {!! $child->render() !!}
@endforeach