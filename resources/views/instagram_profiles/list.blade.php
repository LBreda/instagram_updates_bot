@foreach(Auth::user()->followedProfiles->sortBy('name') as $profile)
    <tr>
        <td class="text-center">
            <img src="{{ $profile->profile_pic }}" class="img-avatar rounded-circle mr-2">
        </td>
        <td>
            <a href="https://instagram.com/{{ $profile->name  }}">{{ '@'.$profile->name }}</a>
        </td>
        <td class="d-none d-sm-table-cell">
            {{ $profile->full_name }}
        </td>
        <td class="d-none d-sm-table-cell">
            <span class="badge badge-{{ $profile->is_private ? 'danger' : 'success' }}">
                @if($profile->is_private)
                    private
                @else
                    ok
                @endif
            </span>
        </td>
        <th scope="col">
            <button class="btn btn-danger" type="button" data-toggle="modal" data-target="#removeIgModal"
                    data-action="{{ route('instagramProfiles.destroy', [$profile]) }}" data-name="{{ $profile->name }}">
                <i class="fas fa-trash"></i>
            </button>
        </th>
    </tr>
@endforeach