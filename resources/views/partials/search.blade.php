<div class="absolute search-form w-1/2 border rounded border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900 focus-within:bg-gray-50 dark:focus-within:bg-gray-800 focus-within:ring focus-within:border-transparent text-gray-400 dark:text-gray-400 focus-within:text-gray-600 dark:focus-within:text-gray-200">

    <label class="flex items-center">
        <span class="material-icons mx-2">search</span>

        <input name="q"
               type="search"
               data-search-form
               autocomplete="off"
               placeholder="Search"
               class="bg-transparent py-2 w-full focus:outline-none">

        <span class="flex items-center mx-2 px-2 border border-gray-200 dark:border-gray-700 rounded">
            <kbd class="flex items-center font-sans">
                <abbr class="search-hotkey search-hotkey--macos" title="Command">⌘</abbr>
                <abbr class="search-hotkey search-hotkey--generic" title="Control">CTRL</abbr>
            </kbd>
            <kbd class="font-sans">K</kbd>
        </span>
    </label>

    <div class="search-form__search-results mx-2 mt-2 py-4">
        <span class="block mx-auto search-results__no-results text-gray-400 text-sm">No results found</span>
    </div>
</div>
