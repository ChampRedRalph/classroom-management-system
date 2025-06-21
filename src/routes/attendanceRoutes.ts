import { Router } from 'express';
import AttendanceController from '../controllers/attendanceController';

const router = Router();
const attendanceController = new AttendanceController();

router.post('/attendance', attendanceController.markAttendance.bind(attendanceController));
router.get('/attendance/:studentId', attendanceController.getAttendance.bind(attendanceController));
router.put('/attendance/:studentId', attendanceController.updateAttendance.bind(attendanceController));

export default function setAttendanceRoutes(app) {
    app.use('/api', router);
}