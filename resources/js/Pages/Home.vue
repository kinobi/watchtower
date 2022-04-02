<template>
    <Head title="WatchTower"/>

    <div class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
        <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="text-sm text-gray-700 underline">
            Dashboard
        </Link>

        <template v-else>
            <Link :href="route('login')" class="text-sm text-gray-700 underline">
                Log in
            </Link>
        </template>
    </div>

    <div class="container mx-auto pt-40 pb-6 text-center text-black">
        <h1 class="md:text-6xl sm:text-5xl text-4xl font-bold tracking-tight">
            <svg class="w-12 mx-auto" viewBox="0 0 64 64" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"><g fill="none" stroke="#336699" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="1.5" class="stroke-3c5a64"><path d="M12 24V12.412C7.054 17.462 4 24.373 4 32c0 9.513 4.749 17.912 12 22.973V26l-4-2zM24 5.166V10l4-2V4.289M16 9.027V14l4-2V6.698M36 4.289V8l4 2V5.166M44 6.698V12l4 2V9.027M52 12.412V24l-4 2v28.973C55.252 49.913 60 41.514 60 32c0-7.627-3.054-14.539-8-19.588zM38 28v31.339a27.759 27.759 0 0 0 4-1.198V30l-4-2zM16 26l16-8v42"></path><path
                d="M32 4v10l20 10M12 24l20-10M32 18l16 8"></path></g></svg>
            WatchTower
        </h1>

    </div>

    <div class="max-w-2xl mx-auto">
        <div class="relative overflow-x-auto shadow-md">

            <table class="w-full text-sm text-left text-gray-500">
                <tbody>
                <tr v-for="url in urls"
                    class="bg-white border-b hover:bg-gray-50">
                    <th scope="row" class="px-6 py-4 text-gray-900">
                        <span class="font-medium uppercase">{{ url.title }}</span><br>
                        {{ url.annotation.note }}
                    </th>
                    <td class="px-6 py-4">
                        <a :href="route('url.uri', {'url': url.id})" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                                <path class="heroicon-ui"
                                      d="M19 6.41L8.7 16.71a1 1 0 1 1-1.4-1.42L17.58 5H14a1 1 0 0 1 0-2h6a1 1 0 0 1 1 1v6a1 1 0 0 1-2 0V6.41zM17 14a1 1 0 0 1 2 0v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7c0-1.1.9-2 2-2h5a1 1 0 0 1 0 2H5v12h12v-5z"/>
                            </svg>
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import {Head, Link} from '@inertiajs/inertia-vue3';

export default {
    components: {
        Head,
        Link,
    },
    props: {
        urls: Object,
    },
}
</script>
