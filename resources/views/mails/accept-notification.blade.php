<p>{{__('Thank for accepting the consent options.  A summary of your preferences is shown below')}}</p>
<hr>
@foreach($consentOptions as $consentOption)

    <h2>{{$consentOption}}</h2>

    <div style="margin-bottom: 20px">{!!$consentOption->text!!}</div>

    <h4>{{__("You ".($consentOption->pivot->accepted?'accepted':'declined') .' on')}} {{$consentOption->pivot->created_at->format('jS M Y H:i')}}</h4>

    <hr style="margin:20px 0">
@endforeach
