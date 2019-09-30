<template>
    <div class="text-center">
        <div class="text-6xl text-orange-500">
            <slot name="error_code" />
        </div>
        <div class="text-3xl text-gray-600">
            <slot name="title" />
        </div>
        <p class="text-xl">
            <slot name="content" />
        </p>
    </div>
</template>

<script>
    export default {
        name: "Error",
    }
</script>
