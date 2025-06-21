export class GradesController {
    private grades: { studentId: string; subject: string; score: number }[] = [];

    public addGrade(studentId: string, subject: string, score: number): void {
        this.grades.push({ studentId, subject, score });
    }

    public getGrades(studentId: string): { subject: string; score: number }[] {
        return this.grades.filter(grade => grade.studentId === studentId);
    }

    public updateGrade(studentId: string, subject: string, newScore: number): boolean {
        const gradeIndex = this.grades.findIndex(grade => grade.studentId === studentId && grade.subject === subject);
        if (gradeIndex !== -1) {
            this.grades[gradeIndex].score = newScore;
            return true;
        }
        return false;
    }
}