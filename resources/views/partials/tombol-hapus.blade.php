<form action="{{ $url }}" method="POST" class="d-inline" onsubmit="return confirm('{{ $pesan ?? 'Yakin ingin menghapus data ini?' }}')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ $judul ?? 'Hapus' }}"><i class="bi {{ $ikon ?? 'bi-trash' }}"></i></button>
</form>
