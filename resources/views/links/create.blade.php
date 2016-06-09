@extends('main')

@section('content')
    <div class="col-md-8 col-md-offset-2">
        <form action="{{ route('links.store') }}" method="post">
            {{ csrf_field() }}
          <div class="form-group">
            <label for="name">Profile</label>
            <input type="text" class="form-control" name="col" placeholder="Please enter Category name">
          </div>
          <div class="form-group">
            <label for="value">Link</label>
            <input type="text" class="form-control" name="value" placeholder="Please enter Category name">
          </div>
          <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
@endsection
