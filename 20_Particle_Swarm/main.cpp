//
//  main.cpp
//  20_Particle_Swarm
//
//  Created by Max Sprauer on 12/20/17.
//  Copyright © 2017 Max Sprauer. All rights reserved.
//


#include <iostream>
#include <fstream>
#include <sstream>
#include <iterator>
#include <vector>
#include <cmath>

using namespace std;


class Particle {
    public:
        long x, y, z;
        long ox, oy, oz;
        long vx, vy, vz;
        long ax, ay, az;
       // long avector;
        long dist;
       // long totalA;
    int ordinal;
    static int counter;
    
    string *toString() {
        ostringstream stringStream;
        stringStream << this->ordinal << ": "
                << "(" << this->ox << "," << this->oy << "," << this->oz << ") "
//            << "(" << this->vx << "," << this->vy << "," << this->vz << ") "
//            << "(" << this->ax << "," << this->ay << "," << this->az << "): "
            << this->dist;
        return new string(stringStream.str());  // Does this leak?
    }
    
    string *currentCoords() const {
        ostringstream stringStream;
        stringStream << this->ordinal << ": "
            << "(" << this->x << "," << this->y << "," << this->z << ")";
        return new string(stringStream.str());  // Does this leak?
    }
    
    ostream& operator<<(ostream& os)
    {
        os << "(" << this->x << "," << this->y << "," << this->z << ")";
        return os;
    }
    
    // For adjacent_find
    bool operator==(const Particle &p) const
    {
        return ((this->x == p.x) && (this->y == p.y) && (this->z == p.z));
    }
    
    
    /* Not working for sort()     
    bool operator<(const Particle &p) const
    {
        return (this->totalA < p.totalA);
    }
    */
    
};

int Particle::counter = 0;



bool compareDist(const Particle &a, const Particle &b)
{
    return (a.dist < b.dist);
}

bool compareCoords(const Particle &a, const Particle &b)
{
    // no
    // return ((a.x < b.x) && (a.y < b.y) && (a.z < b.z));
    // int val = a.currentCoords()->compare(*b.currentCoords());
    return(pow(abs(a.x), 3) + pow(a.y, 2) + a.z < pow(abs(b.x), 3) + pow(b.y, 2) + b.z);
}

// Overload the >> operator
ifstream& operator>>(ifstream &is, Particle &p)
{
    // p=<-1996,177,-1949>, v=<-288,25,-277>, a=<18,3,19>
    is.ignore(100, '<') >> p.x;
    is.ignore(100, ',') >> p.y;
    is.ignore(100, ',') >> p.z;

    is.ignore(100, '<') >> p.vx;
    is.ignore(100, ',') >> p.vy;
    is.ignore(100, ',') >> p.vz;

    is.ignore(100, '<') >> p.ax;
    is.ignore(100, ',') >> p.ay;
    is.ignore(100, ',') >> p.az;

    // p.totalA = abs(p.ax) + abs(p.ay) + abs(p.az);
    // p.startDistPlusTotalA = p.totalA + abs(p.x) + abs(p.y) + abs(p.z);
    
    p.ox = p.x;
    p.oy = p.y;
    p.oz = p.z;
    
    // Since we're reusing the same object, do this here instead of constructor
    p.ordinal = Particle::counter++;
    p.dist = abs(p.x) + abs(p.y) + abs(p.z);

    
     return is;
}

vector<Particle> *parseFile(char *filename)
{
    vector<Particle> *v = new vector<Particle>();
    Particle p;
    
    //prepare ifs to throw if failbit gets se
    ifstream ifs;
    ios_base::iostate exceptionMask = ifs.exceptions() | ios::failbit;
    ifs.exceptions(exceptionMask);
    
    try {
        ifs.open(filename);
        
        while (ifs >> p) {
            v->push_back(p);    // Copies p
        }
    }
    catch (ios_base::failure& e) {
        cerr << "Couldn't open " << filename << " for reading" << endl
            << strerror(errno) << endl;
    }
    
    return v;
}

void dumpVector(vector<Particle> *v)
{
    vector<Particle>::iterator i;
    
    for (i = v->begin(); i != v->end(); i++) {
            cout << *i->toString() << endl;
    }
}

void cycle(vector<Particle> *v)
{
    vector<Particle>::iterator i;

    for (i = v->begin(); i != v->end(); i++) {
        i->vx += i->ax;
        i->vy += i->ay;
        i->vz += i->az;
        
        i->x += i->vx;
        i->y += i->vy;
        i->z += i->vz;

        i->dist = abs(i->x) + abs(i->y) + abs(i->z);
    }
}

void removeCollisions(vector<Particle> *v)
{
    sort(v->begin(), v->end(), compareCoords);
   
    vector<Particle>::iterator first = adjacent_find(v->begin(), v->end());
    
    while (first != v->end()) {
        cerr << "first: " << *first->currentCoords() << endl;
        
        // first is first dupe
        vector<Particle>::iterator last;
        for (last = first + 1; last != v->end() && *last == *first; last++) {
            cerr << "dupe: " << *last->currentCoords() << endl;
        }

        /* Double-check the sorting
        int c = 0;
        vector<Particle>::iterator x;

        for (x = v->begin(); x != v->end(); x++) {
            if (*x == *first)
                c++;
        }
        */
        
        cerr << "Size before erase: " << v->size() << endl;  // ", Count: " << c << endl;
        v->erase(first, last);
        cerr << "Size after erase: " << v->size() << endl << endl;
        
        
        first = adjacent_find(v->begin(), v->end());
    }
    
}

void findParticleWithLowestDist(vector<Particle> *v)
{
    sort(v->begin(), v->end(), compareDist);
    // cout << *(v->begin())->toString() << endl;
    // cout << *(v->end() - 1)->toString() << endl;
    
    vector<Particle>::iterator i;

    for (i = v->begin(); i < v->begin() + 20; i++) {
        cout << *i->toString() << endl;
    }

    cout << endl;
}

int main(int argc, const char * argv[])
{
    vector<Particle> *pv;
    cout << "Hello, World!\n";
    
    pv = parseFile((char *) "input.txt");
    
    /* Part 1
    for (int i = 0; i < 10000; i++) {
        cycle(pv);
        if (i % 1000 == 0) {
            findParticleWithLowestDist(pv);
        }
    }
    */

    /* Part 2 */
    cout << "Initial size: " << pv->size() << endl;
    for (int i = 0; i < 10000; i++) {
        cycle(pv);
        removeCollisions(pv);
    }

    cout << "Final size: " << pv->size() << endl;
    
    return 0;
}




