export default class User {
    constructor(values = {}) {
        this.id = null
        this.email = null
        this.password = null
        this.password_confirmation = null
        this.name = null
        this.created_at = null
        this.updated_at = null
        this.role = null

        Object.assign(this, values)

        if (this.created_at) {
            this.created_at = new Date(this.created_at)
        }

        if (this.updated_at) {
            this.updated_at = new Date(this.updated_at)
        }
    }

    isAdministrator() {
        return this.role === "administrator"
    }

    mappedForSubmission() {
        const data = {
            email: this.email,
            name: this.name,
            role: this.role,
        }
        if (this.password && this.password === this.password_confirmation) {
            data.password = this.password
            data.password_confirmation = this.password_confirmation
        }
        return data
    }
}
