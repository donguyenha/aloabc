@extends('main')

@section('content')
    <div  class="col-md-8 col-md-offset-2">
        <form action="{{ route('category.store') }}" method="post">
            {{ csrf_field() }}
          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" name="name" placeholder="Please enter Category name">
          </div>
          <button type="submit" class="btn btn-primary">Submit</button>
        </form>

    </div>
@endsection
