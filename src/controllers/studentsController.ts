class StudentsController {
    private students: any[] = [];

    createStudent(req: any, res: any) {
        const { id, name, age, class: studentClass } = req.body;
        const newStudent = { id, name, age, class: studentClass };
        this.students.push(newStudent);
        res.status(201).json(newStudent);
    }

    getStudent(req: any, res: any) {
        const studentId = req.params.id;
        const student = this.students.find(s => s.id === studentId);
        if (student) {
            res.status(200).json(student);
        } else {
            res.status(404).json({ message: 'Student not found' });
        }
    }

    updateStudent(req: any, res: any) {
        const studentId = req.params.id;
        const index = this.students.findIndex(s => s.id === studentId);
        if (index !== -1) {
            const updatedStudent = { ...this.students[index], ...req.body };
            this.students[index] = updatedStudent;
            res.status(200).json(updatedStudent);
        } else {
            res.status(404).json({ message: 'Student not found' });
        }
    }

    deleteStudent(req: any, res: any) {
        const studentId = req.params.id;
        const index = this.students.findIndex(s => s.id === studentId);
        if (index !== -1) {
            this.students.splice(index, 1);
            res.status(204).send();
        } else {
            res.status(404).json({ message: 'Student not found' });
        }
    }
}

export default StudentsController;