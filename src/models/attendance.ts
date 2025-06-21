export class Attendance {
    studentId: string;
    date: Date;
    status: 'present' | 'absent' | 'late';

    constructor(studentId: string, date: Date, status: 'present' | 'absent' | 'late') {
        this.studentId = studentId;
        this.date = date;
        this.status = status;
    }
}