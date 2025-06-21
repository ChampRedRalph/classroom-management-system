export class Grade {
    studentId: string;
    subject: string;
    score: number;

    constructor(studentId: string, subject: string, score: number) {
        this.studentId = studentId;
        this.subject = subject;
        this.score = score;
    }
}