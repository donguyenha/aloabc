@extends('main')

@section('content')
    <div class="col-md-8 col-md-offset-2">
        <form action="{{ route('episodes.store') }}" method="post">
            {{ csrf_field() }}
            <div class="form-group">
                <!--
                 *  should be check again the episode_id and title film
                 *  title film should be display
                -->
                <label for="name">Link ID</label>
                <input type="text" class="form-control" name="film_id" placeholder="Please enter Film ID">
            </div>
            <div class="form-group">
                <label for="name">Episode Number</label>
                <input type="text" class="form-control" name="episode_no" placeholder="Please enter Episode Number">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
@endsection
