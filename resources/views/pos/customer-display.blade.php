<x-app-layout title="Customer Display">
    @push('scripts')
        <style>
            html, body { overflow: hidden; margin: 0; padding: 0; height: 100%; }
        </style>
    @endpush
    <livewire:pos.customer-display />
</x-app-layout>
