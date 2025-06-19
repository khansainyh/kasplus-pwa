{{-- resources/views/components/primary-button.blade.php --}}
<button {{ $attributes->merge([
    'type' => 'submit', 
    'class' => 'w-full bg-[#2D5AF7] hover:bg-[#1f42b3] text-white font-regular py-3 px-4 rounded-[14px] transition-colors duration-200 shadow-md tracking-wide text-xl normal-case text-center'
]) }}>
    {{ $slot }}
</button>