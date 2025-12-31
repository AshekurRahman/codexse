<div class="bg-gray-100 dark:bg-gray-900 p-4 rounded-lg">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden max-w-2xl mx-auto">
        <div class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 flex items-center gap-2">
            <div class="flex gap-1.5">
                <div class="w-3 h-3 rounded-full bg-red-500"></div>
                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                <div class="w-3 h-3 rounded-full bg-green-500"></div>
            </div>
            <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">Email Preview</span>
        </div>
        <div class="p-4">
            <iframe
                srcdoc="{{ $content }}"
                class="w-full border-0 rounded"
                style="min-height: 600px;"
                sandbox="allow-same-origin"
            ></iframe>
        </div>
    </div>
</div>
