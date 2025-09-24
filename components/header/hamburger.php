<?php

/**
 * Hamburger
 * 
 * @author Andrea Musso
 * 
 * @package Foundry
 */
?>

<button x-data="{ open: false }"
    @click="open = !open; $dispatch('toggleNav')"
    class="flex items-center focus:outline-none hamburger" id="hamburger">

    <div class="w-6 h-6 flex items-center justify-center relative">
        <span x-bind:class="open ? 'translate-y-0 rotate-45' : '-translate-y-2'"
            class="transform transition w-full h-0.5 bg-dark absolute rounded-sm"></span>

        <span x-bind:class="open ? 'opacity-0 translate-x-3' : 'opacity-100'"
            class="transform transition w-full h-0.5 bg-dark absolute rounded-sm"></span>

        <span x-bind:class="open ? 'translate-y-0 -rotate-45' : 'translate-y-2'"
            class="transform transition w-full h-0.5 bg-dark absolute rounded-sm"></span>
    </div>
</button>