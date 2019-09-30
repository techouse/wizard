<template>
    <el-card :id="id" :ref="contentRef">
        <template slot="header">
            <div slot="header" class="flex justify-between items-center">
                <slot name="header" />
            </div>
        </template>
        <div class="mt-4">
            <slot name="body" />
        </div>
        <div class="mt-4">
            <slot name="footer" />
        </div>
    </el-card>
</template>

<script>
    export default {
        name: "MainContent",

        props: {
            contentRef: {
                type: String,
                default: "card",
            },
            id: {
                type: String,
                default: "card",
            },
        },
    }
</script>
