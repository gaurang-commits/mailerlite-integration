<x-layout>
    <div class="mt-5 text-center">
        <div class="mt-2 d-flex">
            <a href="{{route('subscribers.index')}}" class="btn btn-secondary">Go back to subscribers list</a>
        </div>
        <div>
            <form id="addSubscriber">
                @csrf
                <div class="row mt-4">
                    <div class="col">
                        <div class="form-outline mb-4">
                            <input type="email" id="email" name="email" class="form-control" required />
                            <label class="form-label" for="email">Email address</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-outline mb-4">
                            <input type="text" id="name" name="name" class="form-control" required />
                            <label class="form-label" for="name">Name</label>
                        </div>
                    </div>

                    <div class="col">
                        <div class="form-outline mb-4">
                            <select id="countries" class="form-control" name="country">
                                @foreach ($countries as $key => $value)
                                <option value="{{$value}}">{{$value}}</option>
                                @endforeach
                            </select>
                            <label class="form-label" for="country">Country</label>
                        </div>
                    </div>
                    <!-- Submit button -->
                    <div class="mx-auto text-center">
                        <button type="submit" class="btn btn-primary btn-lg">Add Subscriber</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layout>