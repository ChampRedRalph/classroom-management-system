export class AttendanceController {
    private attendanceRecords: { studentId: string; date: string; status: string }[] = [];

    public markAttendance(studentId: string, date: string, status: string): void {
        this.attendanceRecords.push({ studentId, date, status });
    }

    public getAttendance(studentId: string): { date: string; status: string }[] {
        return this.attendanceRecords.filter(record => record.studentId === studentId);
    }

    public updateAttendance(studentId: string, date: string, status: string): void {
        const record = this.attendanceRecords.find(record => record.studentId === studentId && record.date === date);
        if (record) {
            record.status = status;
        }
    }
}