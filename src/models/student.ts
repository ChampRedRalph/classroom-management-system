export class Student {
    id: number;
    name: string;
    age: number;
    class: string;

    constructor(id: number, name: string, age: number, className: string) {
        this.id = id;
        this.name = name;
        this.age = age;
        this.class = className;
    }
}