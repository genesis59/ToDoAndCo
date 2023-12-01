import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static values = {
        deadLine: String,
        day: String,
        hour: String,
        minute: String,
        second: String,
        finished: String,
    }
    connect() {
        const deadlineDate = new Date(this.deadLineValue);
        this.interval = setInterval(() => {
            const now = new Date();
            const difference = deadlineDate - now;
            this.element.innerHTML = this.formatTime(difference);

            if (difference <= 0) {
                clearInterval(this.interval);
            }
        }, 1000);
    }

    disconnect() {
        clearInterval(this.interval);
    }

    formatTime(milliseconds) {
        const seconds = Math.floor(milliseconds / 1000);
        const minutes = Math.floor(seconds / 60);
        const hours = Math.floor(minutes / 60);
        const days = Math.floor(hours / 24);

        const remainingHours = hours % 24;
        const remainingMinutes = minutes % 60;
        const remainingSeconds = seconds % 60;

        const formattedTime = [];
        if (days > 0) {
            formattedTime.push(`${days} ${this.dayValue}${days > 1 ? 's' : ''}`);
        }

        if (remainingHours > 0) {
            formattedTime.push(`${remainingHours} ${this.hourValue}${remainingHours > 1 ? 's' : ''}`);
        }

        if (remainingMinutes > 0) {
            formattedTime.push(`${remainingMinutes} ${this.minuteValue}${remainingMinutes > 1 ? 's' : ''}`);
        }

        if (remainingSeconds > 0) {
            formattedTime.push(`${remainingSeconds} ${this.secondValue}${remainingSeconds > 1 ? 's' : ''}`);
        }

        if(formattedTime.length === 0){
            formattedTime.push(this.finishedValue)
        }
        return formattedTime.join(' ');
    }
}
