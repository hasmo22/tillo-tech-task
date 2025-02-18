<!DOCTYPE html>
<html>
<head>
    <title>Tillo Technical Test - PHP</title>
    <link rel="stylesheet" href="https://unpkg.com/tailwindcss@1.4.6/dist/base.min.css">
    <link rel="stylesheet" href="https://unpkg.com/tailwindcss@1.4.6/dist/components.min.css">
    <link rel="stylesheet" href="https://unpkg.com/@tailwindcss/typography@0.1.2/dist/typography.min.css">
    <link rel="stylesheet" href="https://unpkg.com/tailwindcss@1.4.6/dist/utilities.min.css">
</head>
<body>
    <div class="container mx-auto mt-10 px-4">
        <div class="prose lg:prose-xl mb-4 lg:mb-8">
            <h1>Tillo Technical Test</h1>
            <h2>PHP Task 2 - Implementation</h2>
            <div>
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Count the number of orders that were...
                    </h3>
                    <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <dl>
                                    <dt class="text-sm leading-5 font-medium text-gray-500 truncate">
                                        FREE
                                    </dt>
                                    <dd class="mt-1 text-3xl leading-9 font-semibold text-gray-900">
                                        {{ $freeOrdersCount }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <dl>
                                    <dt class="text-sm leading-5 font-medium text-gray-500 truncate">
                                        Placed in GBP
                                    </dt>
                                    <dd class="mt-1 text-3xl leading-9 font-semibold text-gray-900">
                                        {{ $gbpOrdersCount }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <dl>
                                    <dt class="text-sm leading-5 font-medium text-gray-500 truncate">
                                        Shipped to Essex
                                    </dt>
                                    <dd class="mt-1 text-3xl leading-9 font-semibold text-gray-900">
                                        {{ $essexOrdersCount }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4 sm:mt-8">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Sum the cost of orders that were...
                    </h3>
                    <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <dl>
                                    <dt class="text-sm leading-5 font-medium text-gray-500 truncate">
                                        Placed in GBP and were £100 or more
                                    </dt>
                                    <dd class="mt-1 text-3xl leading-9 font-semibold text-gray-900">
                                        £{{ $gbpOver100Sum }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <dl>
                                    <dt class="text-sm leading-5 font-medium text-gray-500 truncate">
                                        Placed in GBP
                                    </dt>
                                    <dd class="mt-1 text-3xl leading-9 font-semibold text-gray-900">
                                        £{{ $allGbpSum }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <dl>
                                    <dt class="text-sm leading-5 font-medium text-gray-500 truncate">
                                        Placed in GBP and were shipped to Essex
                                    </dt>
                                    <dd class="mt-1 text-3xl leading-9 font-semibold text-gray-900">
                                        £{{ $gbpEssexSum }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-white border-t border-gray-300 container mx-auto mt-8 py-12 px-4 sm:px-6 md:flex md:items-center md:justify-between lg:px-8">
        <div class="mt-8 md:mt-0 md:order-1">
            <p class="text-center text-base leading-6 text-gray-400">
                &copy; 2020 Tillo. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>