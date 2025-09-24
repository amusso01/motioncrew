<?php

/**
 * Hamburger
 * 
 * @author Andrea Musso
 * 
 * @package Foundry
 */
?>

<button @click="navOpen = !navOpen"
    class="flex items-center focus:outline-none hamburger" id="hamburger">

    <div class="w-6 h-6 flex items-center justify-center relative cursor-pointer">
        <span x-bind:class="navOpen ? 'translate-y-0 rotate-45' : '-translate-y-2'"
            class="transform transition w-full h-0.75 bg-dark absolute rounded-md"></span>

        <span x-bind:class="navOpen ? 'opacity-0 translate-x-3' : 'opacity-100'"
            class="transform transition w-full h-0.75 bg-dark absolute rounded-md"></span>

        <span x-bind:class="navOpen ? 'translate-y-0 -rotate-45' : 'translate-y-2'"
            class="transform transition w-full h-0.75 bg-dark absolute rounded-md"></span>
    </div>
</button>