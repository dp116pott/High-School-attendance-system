# High-School-attendance-system
Created an attendance management tool for my highschool
Brief layout of system architecture: Uses MySql for the database

This system serves as a dual service tool which provides attendance data logging by the school's designated attendance markers(Beadles)
and that of a management tool to observe the attendance of students by their grade supervisors and the school's senior leadership.

1) The main point of contact for the data entry personnel is a form through which their assigned class(es) are preloaded upon their
   selection of a given instructor. Students are loaded into a drop down window and if marked as late or absent are placed in a bubble
   signifying selection. The major decision hurdle regarding this process was how I would implement the preloading of data however the
   end result was in my database mapping relationships between teachers, the classes they teach, the Beadle and the classes they have access
   to and if they have an overlap between both accesses for multiple classes the Beadle has the option to pick which one in a drop down menu
   on click.
   
2) The Login page acted as the interface between both admin and beadle, this page was organized as such as this software would not be publically
   accessible and since kept in house would be much less likely to malicious attacks.
   
3) The admin back end of this software was curated to allow for maximum flexibility among the grade superviors to be able to manipulate the stored
   records for their particular grade. They were additionally able to create aggregate data reports or individualized reports for children in
   their grade.
4) Finally, the webmaster portal gave them the ability to manage supervisor access and perform a shorthand database migration, this role was designed
   particularly with limited functionality at the client's request wanting a fully decentralized system regarding the data's handling(especially relevant
   as admin does not need to handle the data only receive curated reports from the supervisors)
