<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold">Notificaciones</h2></x-slot>
    <div class="mx-auto max-w-3xl space-y-3 py-8 px-4">
        <form method="POST" action="{{ route('notificaciones.leer-todas') }}" class="text-right">@csrf
            <button class="text-sm text-amber-700 underline">Marcar todas como leídas</button>
        </form>
        @forelse($notificaciones as $n)
            <form method="POST" action="{{ route('notificaciones.leer', $n->id) }}">@csrf
                <button class="w-full rounded-lg border p-4 text-left shadow-sm hover:bg-amber-50 {{ $n->read_at ? 'bg-white text-gray-500' : 'bg-amber-50 font-medium' }}">
                    <span class="block text-sm font-semibold">{{ $n->data['titulo'] ?? 'Notificación' }}</span>
                    <span class="block text-sm">{{ $n->data['mensaje'] ?? '' }}</span>
                    <span class="block text-xs text-gray-400">{{ $n->created_at->diffForHumans() }}</span>
                </button>
            </form>
        @empty
            <p class="rounded bg-white p-6 text-center text-gray-500 shadow">No tienes notificaciones.</p>
        @endforelse
        {{ $notificaciones->links() }}
    </div>
</x-app-layout>
