<?php

/**
 * The template for displaying frontpage by default
 *
 * @author Andrea Musso
 *
 * @package foundry
 */

get_header();
?>

<section class="site-hero">

	<?php get_template_part('components/front/hero'); ?>

</section>

<main class="main homepage-main" role="main">

	<div class="content-block">
		<div class="content-max">
			<h1 class="size-h2 font-monospace text-link">Hello World</h1>
			<p class="text-link size-h2">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.</p>
			<a href="#" class="text-link size-h3">Link</a>


			<!-- Modal -->
			<!-- An Alpine.js and Tailwind CSS component by https://pinemix.com -->
			<div x-data="{ open: false }" x-on:keydown.esc.prevent="open = false">
				<!-- Placeholder -->
				<div
					class="flex flex-col items-center justify-center gap-5 rounded-lg border-2 border-dashed border-zinc-200/75 bg-zinc-50 px-4 py-44 text-sm font-medium dark:border-zinc-700 dark:bg-zinc-950/25">
					<!-- Modal Toggle Button -->
					<button
						x-on:click="open = true"
						type="button"
						class="inline-flex items-center justify-center gap-2 rounded-lg border border-zinc-800 bg-zinc-800 px-3 py-2 text-sm font-medium leading-5 text-white hover:border-zinc-900 hover:bg-zinc-900 hover:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-500/50 active:border-zinc-700 active:bg-zinc-700 dark:border-zinc-700/50 dark:bg-zinc-700/50 dark:ring-zinc-700/50 dark:hover:border-zinc-700 dark:hover:bg-zinc-700/75 dark:active:border-zinc-700/50 dark:active:bg-zinc-700/50">
						Open Modal
					</button>
					<!-- END Modal Toggle Button -->
				</div>
				<!-- END Placeholder -->

				<!-- Modal Backdrop -->
				<div
					x-cloak
					x-show="open"
					x-transition:enter="transition ease-out duration-300"
					x-transition:enter-start="opacity-0"
					x-transition:enter-end="opacity-100"
					x-transition:leave="transition ease-in duration-200"
					x-transition:leave-start="opacity-100"
					x-transition:leave-end="opacity-0"
					x-bind:aria-hidden="!open"
					tabindex="-1"
					role="dialog"
					class="z-90 fixed inset-0 overflow-y-auto overflow-x-hidden bg-zinc-900/75 p-4 backdrop-blur-xs will-change-auto lg:p-8">
					<!-- Modal Dialog -->
					<div
						x-cloak
						x-show="open"
						x-on:click.away="open = false"
						x-transition:enter="transition ease-out duration-300"
						x-transition:enter-start="opacity-0 scale-90 -translate-y-full"
						x-transition:enter-end="opacity-100 scale-100 translate-y-0"
						x-transition:leave="transition ease-in duration-150"
						x-transition:leave-start="opacity-100 scale-100 translate-y-0"
						x-transition:leave-end="opacity-0 scale-125 translate-y-full"
						role="document"
						class="mx-auto flex w-full max-w-md flex-col overflow-hidden rounded-lg bg-white shadow-xs will-change-auto dark:bg-zinc-800 dark:text-zinc-100">
						<div
							class="flex items-center justify-between bg-zinc-50 px-5 py-4 dark:bg-zinc-700/20">
							<h3 class="text-lg font-bold">Modal Title</h3>
							<div class="-my-4">
								<button
									x-on:click="open = false"
									type="button"
									class="inline-flex items-center justify-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 py-2 text-xs font-semibold leading-5 text-zinc-800 hover:border-zinc-300 hover:text-zinc-900 hover:shadow-xs focus:ring-zinc-300/25 active:border-zinc-200 active:shadow-none dark:border-zinc-700 dark:bg-transparent dark:text-zinc-300 dark:hover:border-zinc-600 dark:hover:text-zinc-200 dark:focus:ring-zinc-600/50 dark:active:border-zinc-700">
									<svg
										class="hi-solid hi-x -mx-1 inline-block size-4"
										fill="currentColor"
										viewBox="0 0 20 20"
										xmlns="http://www.w3.org/2000/svg">
										<path
											fill-rule="evenodd"
											d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
											clip-rule="evenodd"></path>
									</svg>
								</button>
							</div>
						</div>
						<div class="grow p-5">
							<p class="text-sm/relaxed">Modal content..</p>
						</div>
						<div
							class="flex items-center justify-end gap-1.5 border-t border-zinc-100 px-5 py-4 dark:border-zinc-700/50">
							<button
								x-on:click="open = false"
								type="button"
								class="inline-flex items-center justify-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm font-semibold leading-5 text-zinc-800 hover:border-zinc-300 hover:text-zinc-900 hover:shadow-xs focus:ring-zinc-300/25 active:border-zinc-200 active:shadow-none dark:border-zinc-700 dark:bg-transparent dark:text-zinc-300 dark:hover:border-zinc-600 dark:hover:text-zinc-200 dark:focus:ring-zinc-600/50 dark:active:border-zinc-700">
								Close
							</button>
							<button
								x-on:click="open = false"
								type="button"
								class="inline-flex items-center justify-center gap-2 rounded-lg border border-zinc-800 bg-zinc-800 px-3 py-2 text-sm font-medium leading-5 text-white hover:border-zinc-900 hover:bg-zinc-900 hover:text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-500/50 active:border-zinc-700 active:bg-zinc-700 dark:border-zinc-700/50 dark:bg-zinc-700/50 dark:ring-zinc-700/50 dark:hover:border-zinc-700 dark:hover:bg-zinc-700/75 dark:active:border-zinc-700/50 dark:active:bg-zinc-700/50">
								Save changes
							</button>
						</div>
					</div>
					<!-- END Modal Dialog -->
				</div>
				<!-- END Modal Backdrop -->
			</div>
			<!-- END Modal -->

		</div>
	</div>




</main>

<?php get_footer(); ?>