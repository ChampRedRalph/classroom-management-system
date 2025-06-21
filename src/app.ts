import express from 'express';
import { setStudentsRoutes } from './routes/studentsRoutes';
import { setAttendanceRoutes } from './routes/attendanceRoutes';
import { setGradesRoutes } from './routes/gradesRoutes';

const app = express();
const PORT = process.env.PORT || 3000;

app.use(express.json());

setStudentsRoutes(app);
setAttendanceRoutes(app);
setGradesRoutes(app);

app.listen(PORT, () => {
    console.log(`Server is running on port ${PORT}`);
});