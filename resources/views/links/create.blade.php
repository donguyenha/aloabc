@extends('main')

@section('content')
    <div class="col-md-8 col-md-offset-2">
        <form action="{{ route('links.store') }}" method="post">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="name">Link Film</label>
                <input type="text" class="form-control" name="link" placeholder="Please enter Link Film">
            </div>
            <div class="form-group">
                <!--
                 *  should be check again the episode_id and title film
                 *  title film should be display
                -->
                <label for="name">Episode ID</label>
                <input type="text" class="form-control" name="episode_id" placeholder="Please enter Episode ID">
            </div>
            <div class="form-group">
                <label for="profile">Profile</label>
                <select name="profile" class="form-control">
                    @foreach ($profiles as $profile)
                        <option value="{{ $profile }}">{{ $profile }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
@endsection
