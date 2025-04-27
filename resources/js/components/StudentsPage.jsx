import {
    Box,
    Button,
    Card,
    CardContent,
    Container,
    Grid,
    InputLabel,
    MenuItem,
    Modal,
    Select,
    Table,
    TableBody,
    TableCell,
    TableContainer,
    TableHead,
    TableRow,
    TextField,
    Typography,
    Pagination
  } from '@mui/material';
  import { UploadFile, Search, Add } from '@mui/icons-material';
  import { useState } from 'react';
  
  export default function StudentPage() {
    const [uploadModalOpen, setUploadModalOpen] = useState(false);
    const [scheduleModalOpen, setScheduleModalOpen] = useState(false);
  
    return (
      <>
        {/* Navbar and Sidebar */}
        @include('layout.navbar')
        @include('layout.sidebar')
  
        <Container sx={{ mt: 10, mb: 4 }}>
          {/* Heading */}
          <Box mb={3}>
            <Typography variant="h4" fontWeight="bold" color="primary.dark">
              Students
            </Typography>
            <Typography variant="body2" color="text.secondary">Manage / Student</Typography>
          </Box>
  
          {/* Form Cards */}
          <Grid container spacing={2}>
            @foreach (['Course', 'Block', 'Year Level', 'Status'] as $label)
            <Grid item xs={12} sm={6} md={3}>
              <Card variant="outlined">
                <CardContent>
                  <Typography variant="subtitle1" fontWeight="bold">{!! $label !!}</Typography>
                  @if ($label == 'Course')
                  <TextField fullWidth placeholder="Enter Course" size="small" />
                  @else
                  <Select fullWidth displayEmpty size="small" defaultValue="">
                    <MenuItem disabled value="">{!! "Select {$label}" !!}</MenuItem>
                    @if ($label == 'Block')
                    @foreach (['A', 'B', 'C', 'D'] as $option)
                    <MenuItem value="{!! $option !!}">{!! $option !!}</MenuItem>
                    @endforeach
                    @elseif ($label == 'Year Level')
                    @foreach (range(1, 4) as $year)
                    <MenuItem value="{!! $year !!}">{!! $year !!}</MenuItem>
                    @endforeach
                    @else
                    <MenuItem value="Enrolled">Enrolled</MenuItem>
                    <MenuItem value="Unenrolled">Unenrolled</MenuItem>
                    @endif
                  </Select>
                  @endif
                </CardContent>
              </Card>
            </Grid>
            @endforeach
          </Grid>
  
          {/* Import CSV & Search */}
          <Grid container spacing={2} alignItems="center" sx={{ mt: 4 }}>
            <Grid item xs={12} md={6}>
              <Button
                variant="contained"
                startIcon={<Add />}
                onClick={() => setUploadModalOpen(true)}
              >
                Import CSV
              </Button>
            </Grid>
            <Grid item xs={12} md={6} display="flex" justifyContent="flex-end">
              <Box display="flex" gap={1}>
                <TextField placeholder="Enter search..." size="small" />
                <Button variant="contained" color="success" startIcon={<Search />}>
                  Search
                </Button>
              </Box>
            </Grid>
          </Grid>
  
          {/* Student List */}
          <Card variant="outlined" sx={{ mt: 5, p: 2 }}>
            <Typography variant="h6" fontWeight="bold" color="primary.dark" mb={2}>
              Student List
            </Typography>
  
            @if($students->isEmpty())
            <Typography>No students found.</Typography>
            @else
            <TableContainer>
              <Table>
                <TableHead>
                  <TableRow>
                    <TableCell>No</TableCell>
                    <TableCell>Student ID</TableCell>
                    <TableCell>Name</TableCell>
                    <TableCell>Gender</TableCell>
                    <TableCell>Course</TableCell>
                    <TableCell>Year</TableCell>
                    <TableCell>Units</TableCell>
                    <TableCell>Section</TableCell>
                    <TableCell>Contact Number</TableCell>
                    <TableCell>Birth Date</TableCell>
                    <TableCell>Address</TableCell>
                  </TableRow>
                </TableHead>
                <TableBody>
                  @foreach($students as $student)
                  <TableRow>
                    <TableCell>{{ $student->no }}</TableCell>
                    <TableCell>{{ $student->id_number }}</TableCell>
                    <TableCell>{{ $student->name }}</TableCell>
                    <TableCell>{{ $student->gender }}</TableCell>
                    <TableCell>{{ $student->course }}</TableCell>
                    <TableCell>{{ $student->year }}</TableCell>
                    <TableCell>{{ $student->units }}</TableCell>
                    <TableCell>{{ $student->section }}</TableCell>
                    <TableCell>{{ $student->contact_no }}</TableCell>
                    <TableCell>{{ $student->birth_date }}</TableCell>
                    <TableCell>{{ $student->address }}</TableCell>
                  </TableRow>
                  @endforeach
                </TableBody>
              </Table>
            </TableContainer>
            @endif
  
            {/* Pagination */}
            <Box display="flex" justifyContent="flex-end" mt={3}>
              <Pagination count={3} variant="outlined" shape="rounded" />
            </Box>
  
            {/* Generate Schedule */}
            <Box textAlign="center" mt={4}>
              <Button variant="contained" onClick={() => setScheduleModalOpen(true)}>
                Generate Schedule
              </Button>
            </Box>
          </Card>
  
          {/* Upload CSV Modal */}
          <Modal open={uploadModalOpen} onClose={() => setUploadModalOpen(false)}>
            <Box sx={modalStyle}>
              <Typography variant="h6" mb={2}>Upload CSV File</Typography>
              <form id="csvForm" action="{{ route('import') }}" method="post" enctype="multipart/form-data" onSubmit="return validateCSV();">
                @csrf
                <Button component="label" variant="outlined" fullWidth startIcon={<UploadFile />}>
                  Upload CSV
                  <input type="file" hidden name="importFile" accept=".csv" required />
                </Button>
                <Box display="flex" justifyContent="space-between" mt={3}>
                  <Button variant="outlined" color="secondary" onClick={() => setUploadModalOpen(false)}>
                    Cancel
                  </Button>
                  <Button type="submit" variant="contained">
                    Upload
                  </Button>
                </Box>
              </form>
            </Box>
          </Modal>
  
          {/* Generate Schedule Modal */}
          <Modal open={scheduleModalOpen} onClose={() => setScheduleModalOpen(false)}>
            <Box sx={modalStyle}>
              <Typography variant="h6" mb={2}>Generate Schedule</Typography>
              <form id="generateScheduleForm" method="POST" action="{{ route('generate.memo.pdf') }}">
                @csrf
                <Grid container spacing={2}>
                  <Grid item xs={12}>
                    <InputLabel>Course</InputLabel>
                    <Select fullWidth displayEmpty name="course" required>
                      <MenuItem disabled value="">Select Course</MenuItem>
                      <MenuItem value="BSIT">BSIT</MenuItem>
                      <MenuItem value="BSIS">BSIS</MenuItem>
                    </Select>
                  </Grid>
                  <Grid item xs={12}>
                    <InputLabel>Block</InputLabel>
                    <Select fullWidth displayEmpty name="block" required>
                      <MenuItem disabled value="">Select Block</MenuItem>
                      <MenuItem value="A">A</MenuItem>
                      <MenuItem value="B">B</MenuItem>
                      <MenuItem value="C">C</MenuItem>
                    </Select>
                  </Grid>
                  <Grid item xs={12}>
                    <InputLabel>Year</InputLabel>
                    <Select fullWidth displayEmpty name="year" required>
                      <MenuItem disabled value="">Select Year</MenuItem>
                      <MenuItem value="1">1</MenuItem>
                      <MenuItem value="2">2</MenuItem>
                      <MenuItem value="3">3</MenuItem>
                      <MenuItem value="4">4</MenuItem>
                    </Select>
                  </Grid>
                  <Grid item xs={12}>
                    <TextField fullWidth label="Venue" name="venue" required />
                  </Grid>
                  <Grid item xs={12}>
                    <TextField fullWidth type="date" name="date" required />
                  </Grid>
                  <Grid item xs={12} display="flex" justifyContent="space-between">
                    <Button variant="outlined" color="secondary" onClick={() => setScheduleModalOpen(false)}>
                      Cancel
                    </Button>
                    <Button type="submit" variant="contained">
                      Generate
                    </Button>
                  </Grid>
                </Grid>
              </form>
            </Box>
          </Modal>
        </Container>
      </>
    );
  }
  
  // Styling for Modal Box
  const modalStyle = {
    position: 'absolute',
    top: '50%',
    left: '50%',
    transform: 'translate(-50%, -50%)',
    width: 400,
    bgcolor: 'background.paper',
    boxShadow: 24,
    p: 4,
    borderRadius: 2,
  };
  