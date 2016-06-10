@extends('main')

@section('content')
    <div class="col-md-8 col-md-offset-2">
        <form action="{{ route('thumbnails.store') }}" method="post">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="name">Link Thumbnail</label>
                <input type="text" class="form-control" name="link" placeholder="Please enter Link Thumbnail">
            </div>
            <div class="form-group">
                <label for="profile">Profile</label>
                <select name="profile" class="form-control">
                    <option value="small">Small</option>
                    <option value="medium">Medium</option>
                    <option value="large">Large</option>
                </select>
            </div>
            <div class="form-group">
                <label for="film">Film ID</label>
                <input type="text" class="form-control" name="film_id" placeholder="Please enter film ID">
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
@endsection
