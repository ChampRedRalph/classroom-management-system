export interface Student {
    id: number;
    name: string;
    age: number;
    class: string;
}

export interface AttendanceRecord {
    studentId: number;
    date: string;
    status: 'present' | 'absent' | 'late';
}

export interface GradeRecord {
    studentId: number;
    subject: string;
    score: number;
}