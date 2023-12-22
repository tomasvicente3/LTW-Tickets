drop table if exists FAQEntry;

drop table if exists Change;

drop table if exists Message;

drop table if exists Ticket;

drop table if exists AgentDepartment;

drop table if exists Department;

drop table if exists Admin;

drop table if exists Agent;

drop table if exists Client;

create table
    Client (
        id INTEGER PRIMARY KEY,
        email text UNIQUE NOT NULL,
        firstName text NOT NULL,
        lastName text NOT NULL,
        username text UNIQUE NOT NULL,
        password text NOT NULL
    );

create table
    Agent (
        id INTEGER PRIMARY KEY,
        CONSTRAINT AgentClientFK FOREIGN KEY (id) REFERENCES Client (id) ON UPDATE CASCADE ON DELETE CASCADE
    );

create table
    Admin (
        id INTEGER PRIMARY KEY,
        CONSTRAINT AdminAgentFK FOREIGN KEY (id) REFERENCES Agent (id) ON UPDATE CASCADE ON DELETE CASCADE
    );

create table
    Department (id INTEGER PRIMARY KEY, name TEXT NOT NULL UNIQUE);

create table
    AgentDepartment (
        idAgent INTEGER,
        idDepartment INTEGER,
        CONSTRAINT AgentFK FOREIGN KEY (idAgent) REFERENCES Agent (id) ON UPDATE CASCADE ON DELETE CASCADE,
        CONSTRAINT DepartmentFK FOREIGN KEY (idDepartment) REFERENCES Department (id) ON UPDATE CASCADE ON DELETE CASCADE
    );

create table
    Ticket (
        id INTEGER PRIMARY KEY,
        title text NOT NULL,
        date text NOT NULL,
        description text NOT NULL,
        hashtag text,
        status text NOT NULL CHECK (status IN ('open', 'assigned', 'closed')),
        priority text NOT NULL CHECK (
            priority IN ('low', 'medium', 'high', 'very high')
        ),
        department INTEGER,
        agent INTEGER,
        client INTEGER NOT NULL,
        answer text,
        faqAnswer INTEGER,
        CONSTRAINT DepartmentFK FOREIGN KEY (department) REFERENCES Department (id) ON UPDATE CASCADE ON DELETE SET NULL,
        CONSTRAINT AgentFK FOREIGN KEY (agent) REFERENCES Agent (id) ON UPDATE CASCADE ON DELETE SET NULL,
        CONSTRAINT ClientFK FOREIGN KEY (client) REFERENCES Client (id) ON UPDATE CASCADE ON DELETE CASCADE CONSTRAINT FAQEntryFK FOREIGN KEY (faqAnswer) REFERENCES FAQEntry (id) ON UPDATE CASCADE ON DELETE SET NULL
    );

create table
    Message (
        id INTEGER PRIMARY KEY,
        idClient INTEGER,
        idTicket INTEGER,
        content text NOT NULL,
        date text NOT NULL,
        CONSTRAINT MessageClientFK FOREIGN KEY (idClient) REFERENCES Client (idClient) ON UPDATE CASCADE ON DELETE CASCADE,
        CONSTRAINT MessageTicketFK FOREIGN KEY (idTicket) REFERENCES Ticket (idTicket) ON UPDATE CASCADE ON DELETE CASCADE
    );

create table
    Change (
        id INTEGER PRIMARY KEY,
        date TEXT NOT NULL,
        type TEXT NOT NULL CHECK (
            type IN (
                'agent',
                'department',
                'status',
                'hashtag',
                'priority',
                'description',
                'title'
            )
        ),
        oldValue TEXT,
        newValue TEXT,
        author INTEGER NOT NULL,
        ticket INTEGER NOT NULL,
        CONSTRAINT AgentFK FOREIGN KEY (author) REFERENCES Agent (id) ON UPDATE CASCADE ON DELETE SET NULL,
        CONSTRAINT TicketFK FOREIGN KEY (ticket) REFERENCES Ticket (id) ON UPDATE CASCADE ON DELETE CASCADE
    );

create table
    FAQEntry (
        id INTEGER PRIMARY KEY,
        title text UNIQUE NOT NULL,
        content text NOT NULL,
        date text NOT NULL,
        agent INTEGER NOT NULL,
        CONSTRAINT AgentFK FOREIGN KEY (agent) REFERENCES Agent (id) ON UPDATE CASCADE ON DELETE SET NULL
    );

INSERT INTO
    Department (name)
VALUES
    ("Accounting"),
    ("Human Resources"),
    ("IT"),
    ("Social Media"),
    ("Sales");