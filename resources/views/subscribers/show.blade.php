<x-layout>
    <div class="container mt-5">
        <div class="d-flex mt-2 mb-2">
            <a href="{{route('subscribers.create')}}" class="btn btn-secondary">Add subscriber</a>
        </div>
        <table class="table" id="dataTable">
            <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Country</th>
                    <th scope="col">Subscribed On</th>
                    <th scope="col">Subscribed At</th>
                    <th scope="col">Delete</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <x-modals.email :countries=' config("countries")' />
</x-layout>