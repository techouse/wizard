<template>
    <el-card>
        <template slot="header">
            <div class="flex justify-between items-center">
                <slot name="header" />
            </div>
        </template>
        <el-form :id="id" :ref="formRef" v-loading="loading" :model="model" :rules="rules"
                 :label-width="labelWidth" :label-position="labelPosition" @submit.native.prevent
        >
            <div class="mt-4">
                <slot name="body" />
            </div>
            <div class="mt-4">
                <slot name="footer" />
            </div>
        </el-form>
    </el-card>
</template>

<script>
    export default {
        name: "MainForm",

        props: {
            id: {
                type: String,
                default: "card-form",
            },
            formRef: {
                type: String,
                default: "form",
            },
            loading: {
                type: Boolean,
                default: false,
            },
            model: {
                type: Object,
                require: true,
            },
            rules: {
                type: Object,
                default: () => ({}),
            },
            labelWidth: {
                type: String,
                default: "140px",
            },
            labelPosition: {
                type: String,
                default: "right",
            },
        },

        data() {
            return {
                width: 0,
                height: 0,
            }
        },

        mounted() {
            this.handleWindowResize()
            window.addEventListener("resize", this.handleWindowResize)
        },

        methods: {
            handleWindowResize() {
                const element = document.getElementById(this.id)
                if (element) {
                    this.$set(this, "width", element.clientWidth)
                    this.$set(this, "height", element.clientHeight)
                }
            },

            validate(callback) {
                return this.$refs[this.formRef].validate(callback)
            },

            validateField(callback) {
                return this.$refs[this.formRef].validateField(callback)
            },

            resetFields() {
                return this.$refs[this.formRef].resetFields()
            },

            clearValidate(callback) {
                return this.$refs[this.formRef].clearValidate(callback)
            },
        },
    }
</script>
